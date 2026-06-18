<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tender;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $users = $this->buildSubconQuery($request)
            ->orderBy($this->resolveSortBy($request), $this->resolveSortDir($request))
            ->get();

        $projects = Tender::with('selectedSubcon')->latest()->get();

        return view('admin.dashboard', compact('users', 'projects'));
    }

    public function exportUsers(Request $request)
    {
        $format = $request->query('format', 'excel');
        $users = $this->buildSubconQuery($request)
            ->orderBy($this->resolveSortBy($request), $this->resolveSortDir($request))
            ->get();

        if ($format === 'pdf') {
            return $this->exportUsersPdf($users);
        }

        return $this->exportUsersExcel($users);
    }

    protected function buildSubconQuery(Request $request)
    {
        $query = User::query()->where('role', 'subcon');

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('company_name', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('services_provided', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('grade')) {
            $query->where('cidb_grades', 'like', "%{$request->query('grade')}%");
        }

        return $query;
    }

    protected function resolveSortBy(Request $request): string
    {
        $allowedSorts = ['company_name', 'name', 'cidb_grades', 'status', 'created_at'];
        $sortBy = $request->query('sort_by', 'created_at');
        return in_array($sortBy, $allowedSorts, true) ? $sortBy : 'created_at';
    }

    protected function resolveSortDir(Request $request): string
    {
        return $request->query('sort_dir') === 'asc' ? 'asc' : 'desc';
    }

    protected function exportUsersExcel($users)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Subcontractors');

        $headers = ['Company Name', 'PIC', 'Email', 'CIDB Grade', 'Status', 'Services', 'Registered'];
        foreach ($headers as $col => $header) {
            $sheet->setCellValue(chr(65 + $col) . '1', $header);
        }

        foreach ($users as $index => $user) {
            $row = $index + 2;
            $sheet->setCellValue('A' . $row, $user->company_name);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->email);
            $sheet->setCellValue('D' . $row, is_array($user->cidb_grades) ? implode(', ', $user->cidb_grades) : $user->cidb_grades);
            $sheet->setCellValue('E' . $row, $user->status);
            $sheet->setCellValue('F' . $row, is_array($user->services_provided) ? implode(', ', $user->services_provided) : $user->services_provided);
            $sheet->setCellValue('G' . $row, optional($user->created_at)->format('Y-m-d'));
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'subcontractors-report-' . now()->format('Ymd-His') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    protected function exportUsersPdf($users)
    {
        $html = view('admin.exports.users-pdf', compact('users'))->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="subcontractors-report-' . now()->format('Ymd-His') . '.pdf"',
        ]);
    }

    /**
     * Show Subcontractor Profile
     */
    public function show(int $id)
    {
        $subcon = User::findOrFail($id);
        return view('admin.show', compact('subcon'));
    }

    public function pendingApprovals()
    {
        $pendingSubcontractors = User::query()
            ->where('role', 'subcon')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.pending-approvals', compact('pendingSubcontractors'));
    }

    public function activity()
    {
        $request = request();
        $events = collect();

        $start = $request->query('start_date');
        $end = $request->query('end_date');

        // Helper to check optional date range
        $inRange = function ($time) use ($start, $end) {
            try {
                $t = \Carbon\Carbon::parse($time);
            } catch (\Throwable $e) {
                return false;
            }
            if ($start) {
                $s = \Carbon\Carbon::parse($start)->startOfDay();
                if ($t->lt($s)) return false;
            }
            if ($end) {
                $eDate = \Carbon\Carbon::parse($end)->endOfDay();
                if ($t->gt($eDate)) return false;
            }
            return true;
        };

        Tender::with('selectedSubcon')
            ->whereNotNull('report_path')
            ->latest('updated_at')
            ->get()
            ->each(function ($tender) use ($events, $inRange) {
                $reportData = is_array($tender->report_path)
                    ? $tender->report_path
                    : json_decode($tender->report_path ?? '', true) ?? [];

                foreach ($reportData['files'] ?? [] as $category => $files) {
                    foreach ($files as $file) {
                        $time = $file['uploaded_at'] ?? ($file['reviewed_at'] ?? null);
                        if ($time && $inRange($time)) {
                            $events->push([
                                'time' => $time,
                                'subcon' => $tender->selectedSubcon?->company_name ?? $tender->selectedSubcon?->name,
                                'tender' => $tender->title,
                                'category' => str_replace('_', ' ', ucfirst($category)),
                                'status' => $file['status'] ?? 'pending',
                                'uploader' => $tender->selectedSubcon?->name ?? 'Subcontractor',
                                'type' => 'tender_file',
                            ]);
                        }
                    }
                }
            });

        // Include profile updates from subcontractors (based on updated_at)
        $userQuery = User::query()->where('role', 'subcon');
        if ($start) {
            $userQuery->where('updated_at', '>=', \Carbon\Carbon::parse($start)->startOfDay());
        }
        if ($end) {
            $userQuery->where('updated_at', '<=', \Carbon\Carbon::parse($end)->endOfDay());
        }
        $userQuery->latest('updated_at')->get()->each(function ($user) use ($events, $inRange) {
            // avoid including users without meaningful updates (created_at == updated_at)
            if (!$user->updated_at) return;
            if ($user->created_at && $user->created_at->eq($user->updated_at)) return;

            $time = $user->updated_at->toDateTimeString();
            if ($inRange($time)) {
                $events->push([
                    'time' => $time,
                    'subcon' => $user->company_name ?? $user->name,
                    'tender' => null,
                    'category' => 'Profile Update',
                    'status' => 'updated',
                    'uploader' => $user->name,
                    'type' => 'profile_update',
                ]);
            }
        });

        $events = $events->sortByDesc('time')->take(100);

        return view('admin.activity', compact('events'));
    }

    /**
     * Show Tender Details & Review Submissions
     * This is the method for admin/tenders/show.blade.php
     */
    public function showTender(int $id)
    {
        $project = Tender::with('subcon')->findOrFail($id);

        // Extract files from JSON for the review workspace
        $reportData = $project->report_path ?? ['files' => []];
        $submittedFiles = $reportData['files'] ?? [];

        return view('admin.tenders.show', compact('project', 'submittedFiles'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $subcon = User::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,active,inactive',
        ]);

        $subcon->status = $request->status;
        $subcon->save();

        return back()->with('status', 'Account status updated successfully.');
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('status', 'Subcon removed successfully!');
    }

    /**
     * Approve or Reject a specific file inside the JSON array
     */
    public function updateFileStatus(Request $request, int $id)
    {
        $project = Tender::findOrFail($id);

        $request->validate([
            'category' => 'required|string',
            'index' => 'required|integer',
            'status' => 'required|in:approved,rejected',
            'feedback' => 'required_if:status,rejected|nullable|string'
        ]);

        $reportData = $project->report_path;

        $category = $request->category;
        $index = $request->index;

        if (isset($reportData['files'][$category][$index])) {
            $reportData['files'][$category][$index]['status'] = $request->status;
            $reportData['files'][$category][$index]['feedback'] = $request->feedback;
            $reportData['files'][$category][$index]['reviewed_at'] = now()->toDateTimeString();
        }

        // Save updated JSON
        $project->report_path = $reportData;

        // Recalculate progress based on what YOU approved
        $project->progress_percent = $this->calculateAdminProgress($reportData);

        $project->save();

        return back()->with('status', 'File status updated successfully.');
    }

    /**
     * Progress calculation: Only counts if a category has at least one APPROVED file
     */
    private function calculateAdminProgress(array $reportData)
    {
        $categories = ['site_photos', 'financial_docs', 'invoices'];
        $approvedCount = 0;

        if (!isset($reportData['files'])) return 0;

        foreach($categories as $cat) {
            if(!empty($reportData['files'][$cat])) {
                $hasApproved = collect($reportData['files'][$cat])->contains('status', 'approved');
                if($hasApproved) $approvedCount++;
            }
        }

        return round(($approvedCount / count($categories)) * 100);
    }
}

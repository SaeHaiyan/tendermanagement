<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Tender;
use App\Services\AdminNotificationService;

class SubconController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') return redirect()->route('admin.dashboard');
        if ($user->status === 'pending') return view('subcon.pending-notice');

        $activeProjects = Tender::query()
            ->where('selected_subcon_id', $user->id)
            ->where('work_status', '!=', 'completed')
            ->latest()
            ->get();

        $completedProjects = Tender::query()
            ->where('selected_subcon_id', $user->id)
            ->where('work_status', 'completed')
            ->latest()
            ->get();

        // Build activity events for this subcontractor (approved/rejected/pending uploads)
        $events = collect();
        $tendersWithReports = Tender::query()
            ->where('selected_subcon_id', $user->id)
            ->whereNotNull('report_path')
            ->latest('updated_at')
            ->get();

        foreach ($tendersWithReports as $t) {
            $reportData = is_array($t->report_path) ? $t->report_path : json_decode($t->report_path ?? '', true) ?? [];
            foreach ($reportData['files'] ?? [] as $category => $files) {
                foreach ($files as $idx => $file) {
                    $status = $file['status'] ?? 'pending';
                    $time = $file['reviewed_at'] ?? $file['uploaded_at'] ?? ($t->updated_at ? $t->updated_at->toDateTimeString() : now()->toDateTimeString());
                    $events->push([
                        'time' => $time,
                        'tender_id' => $t->id,
                        'title' => $t->title,
                        'category' => $category,
                        'status' => $status,
                        'feedback' => $file['feedback'] ?? null,
                        'index' => $idx,
                    ]);
                }
            }
        }

        $events = $events->sortByDesc('time')->take(20)->values();

        return view('subcon.dashboard', compact('activeProjects', 'completedProjects', 'events'));
    }

    public function uploadPendingDocuments(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'documents' => 'required|array|min:1',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120',
            'doc_type' => 'nullable|string|in:ssm,cidb,bank,other',
        ]);

        $pendingDocuments = $user->pending_documents ?? [];
        $docType = $request->input('doc_type');

        foreach ($request->file('documents') as $file) {
            $path = $file->store('pending-account-documents/' . $user->id, 'public');
            $pendingDocuments[] = [
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'type' => $docType ?? 'other',
                'status' => 'pending',
                'uploaded_at' => now()->toDateTimeString(),
            ];
        }

        $user->pending_documents = $pendingDocuments;
        $user->save();

        // Append admin notification about uploaded account documents
        try {
            $svc = app(AdminNotificationService::class);
            $svc->append([
                'type' => 'account_documents',
                'subcon_id' => $user->id,
                'subcon' => $user->company_name ?? $user->name,
                'message' => 'Subcontractor uploaded account documents.',
                'link' => route('admin.subcon.show', $user->id),
            ]);
        } catch (\Throwable $e) {
            // ignore notification failures
        }

        return back()->with('success', 'Your documents were uploaded successfully. Admin will review them shortly.');
    }

    public function manage(int $id)
    {
        // Fixed: Use Tender model instead of Project
        $project = Tender::findOrFail($id);

        $reportData = is_array($project->report_path)
            ? $project->report_path
            : json_decode($project->report_path ?? '', true) ?? [];

        $submittedFiles = $reportData['files'] ?? [];

        $hasRejections = false;
        foreach($submittedFiles as $cat => $items) {
            foreach($items as $f) {
                if(isset($f['status']) && $f['status'] === 'rejected') $hasRejections = true;
            }
        }

        return view('subcon.manage-project', compact('project', 'submittedFiles', 'hasRejections'));
    }

    public function documents()
    {
        $user = Auth::user();
        $pendingDocuments = $user->pending_documents ?? [];

        return view('subcon.documents', compact('pendingDocuments'));
    }

    public function uploadDocuments(Request $request)
    {
        return $this->uploadPendingDocuments($request);
    }

    /**
     * Handle initial or additional file uploads for a specific category
     */
    public function updateProgress(Request $request, int $id)
    {
        $project = Tender::findOrFail($id);
        $category = $request->input('category_type');
        $description = $request->input('description'); // Capture the description

        $reportData = $project->report_path ?? ['files' => []];

        if($request->hasFile('documents')) {
            foreach($request->file('documents') as $file) {
                $path = $file->store('tenders/' . $project->id, 'public');

                $reportData['files'][$category][] = [
                    'path' => $path,
                    'status' => 'pending',
                    'feedback' => null,
                    'description' => $description, // Store the description here
                    'uploaded_at' => now()->toDateTimeString()
                ];
            }
        }

        $project->report_path = $reportData;
        $project->progress_percent = $this->calculateProgress($reportData);
        $project->save();

        return back()->with('success', 'Files and description saved successfully.');
    }

    /**
     * Replace a specific file that was rejected
     */
    public function replaceFile(Request $request, int $id)
    {
        $project = Tender::findOrFail($id);
        $category = $request->input('category');
        $index = $request->input('file_index');

        $reportData = $project->report_path;

        if($request->hasFile('replacement')) {
            // Delete old file if it exists to save storage
            if (isset($reportData['files'][$category][$index]['path'])) {
                Storage::disk('public')->delete($reportData['files'][$category][$index]['path']);
            }

            $path = $request->file('replacement')->store('tenders/' . $project->id, 'public');

            // Swap the data at the specific index
            $reportData['files'][$category][$index] = [
                'path' => $path,
                'status' => 'pending',
                'feedback' => null,
                'uploaded_at' => now()->toDateTimeString()
            ];

            $project->report_path = $reportData;
            $project->progress_percent = $this->calculateProgress($reportData);
            $project->save();
        }

        return back()->with('success', 'Replacement file submitted for review.');
    }

    /**
     * Internal logic to determine progress percentage
     */
    private function calculateProgress(array $reportData)
    {
        $categories = ['site_photos', 'financial_docs', 'invoices'];
        $completedCount = 0;

        foreach($categories as $cat) {
            if(!empty($reportData['files'][$cat])) {
                // Category counts as 'done' if there is at least one file that isn't rejected
                $hasValidFile = collect($reportData['files'][$cat])->contains(function($f) {
                    return $f['status'] !== 'rejected';
                });

                if($hasValidFile) $completedCount++;
            }
        }

        // Returns 0, 33, 66, or 100 based on 3 categories
        return round(($completedCount / count($categories)) * 100);
    }
}

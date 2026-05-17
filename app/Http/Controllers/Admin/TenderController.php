<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use App\Models\User;
use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TenderController extends Controller
{
    /**
     * Display a listing of the tenders.
     */
    public function index(Request $request)
    {
        $allowedSorts = ['title', 'deadline', 'work_status', 'required_grade', 'created_at', 'selected_subcon'];
        $sortBy = in_array($request->query('sort_by'), $allowedSorts) ? $request->query('sort_by') : 'created_at';
        $sortDir = $request->query('sort_dir') === 'asc' ? 'asc' : 'desc';

        $query = Tender::with('selectedSubcon');

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('tender_ref_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('work_status', $request->query('status'));
        }

        if ($request->filled('grade')) {
            $query->where('required_grade', 'like', "%{$request->query('grade')}%");
        }

        if ($sortBy === 'selected_subcon') {
            $query->leftJoin('users', 'users.id', '=', 'tenders.selected_subcon_id')
                ->select('tenders.*')
                ->orderBy('users.company_name', $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $tenders = $query->get();

        return view('admin.tenders.index', compact('tenders', 'sortBy', 'sortDir'));
    }

    public function export(Request $request)
    {
        $format = strtolower($request->query('format', 'excel')) === 'pdf' ? 'pdf' : 'excel';
        $allowedSorts = ['title', 'deadline', 'work_status', 'required_grade', 'created_at', 'selected_subcon'];
        $sortBy = in_array($request->query('sort_by'), $allowedSorts) ? $request->query('sort_by') : 'created_at';
        $sortDir = $request->query('sort_dir') === 'asc' ? 'asc' : 'desc';

        $query = $this->buildTenderQuery($request);

        if ($sortBy === 'selected_subcon') {
            $query->leftJoin('users', 'users.id', '=', 'tenders.selected_subcon_id')
                ->select('tenders.*')
                ->orderBy('users.company_name', $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $tenders = $query->get();

        return $format === 'pdf' ? $this->exportTendersPdf($tenders) : $this->exportTendersExcel($tenders);
    }

    protected function buildTenderQuery(Request $request)
    {
        $query = Tender::with('selectedSubcon');

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('tender_ref_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('work_status', $request->query('status'));
        }

        if ($request->filled('grade')) {
            $query->where('required_grade', 'like', "%{$request->query('grade')}%");
        }

        return $query;
    }

    protected function exportTendersExcel($tenders)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tenders');

        $headers = ['Title', 'Reference', 'Grade', 'Services', 'Deadline', 'Status', 'Assignee', 'Progress'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . '1', $header);
        }

        foreach ($tenders as $idx => $tender) {
            $row = $idx + 2;
            $sheet->setCellValue('A' . $row, $tender->title);
            $sheet->setCellValue('B' . $row, $tender->tender_ref_number);
            $sheet->setCellValue('C' . $row, $tender->required_grade);
            $sheet->setCellValue('D' . $row, $tender->required_services);
            $sheet->setCellValue('E' . $row, optional($tender->deadline)->format('Y-m-d'));
            $sheet->setCellValue('F' . $row, $tender->work_status);
            $sheet->setCellValue('G' . $row, optional($tender->selectedSubcon)->company_name);
            $sheet->setCellValue('H' . $row, $tender->progress_percent . '%');
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'tenders-report-' . now()->format('Ymd-His') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    protected function exportTendersPdf($tenders)
    {
        $html = view('admin.exports.tenders-pdf', compact('tenders'))->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="tenders-report-' . now()->format('Ymd-His') . '.pdf"',
        ]);
    }

    public function exportSingle(Request $request, int $id)
    {
        // Fetch tender with its assigned subcontractor records
        $tender = Tender::with('selectedSubcon')->findOrFail($id);
        $format = $request->get('format', 'pdf');

        // Decode file submissions tracking data safely
        $reportData = is_array($tender->report_path)
            ? $tender->report_path
            : json_decode($tender->report_path ?? '', true) ?? [];
        $categories = $reportData['files'] ?? [];

        if ($format === 'excel') {
            // --- 1. QUICK EXCEL DOWNLOAD (CSV Format) ---
            $fileName = 'Tender_Status_Report_' . $tender->id . '_' . date('Ymd') . '.csv';

            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use ($tender, $categories) {
                $file = fopen('php://output', 'w');

                // Corporate Title Headers
                fputcsv($file, ['AITOTENDER PROJECT MONITORING REPORT']);
                fputcsv($file, ['Generated:', date('Y-m-d H:i:s')]);
                fputcsv($file, []); // Empty spacing row

                // Core Tender Meta Metadata Block
                fputcsv($file, ['Project Title', $tender->title]);
                fputcsv($file, ['Work Status', Str::upper($tender->work_status)]);                fputcsv($file, ['Progress Percent', $tender->progress_percent . '%']);
                fputcsv($file, ['Required Grade', 'Grade ' . $tender->required_grade]);
                fputcsv($file, ['Partner Subcon', $tender->selectedSubcon->company_name ?? 'None Assigned']);
                fputcsv($file, []);

                // Submissions Breakdown Section Headers
                fputcsv($file, ['Submission Type', 'Item Index', 'Storage Path Reference', 'Status', 'Reviewer Feedback']);

                $labels = ['site_photos' => 'Site Progress Photos', 'financial_docs' => 'Financial Claims', 'invoices' => 'Tax Invoices'];
                foreach ($labels as $key => $label) {
                    foreach ($categories[$key] ?? [] as $index => $subFile) {
                        fputcsv($file, [
                            $label,
                            'Submission #' . ($index + 1),
                            is_array($subFile) ? ($subFile['path'] ?? '') : $subFile,
                            is_array($subFile) ? ($subFile['status'] ?? 'pending') : 'pending',
                            is_array($subFile) ? ($subFile['feedback'] ?? '') : ''
                        ]);
                    }
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // --- 2. QUICK HTML/PDF OVERVIEW PRINT ---
        // If you haven't installed 'barryvdh/laravel-dompdf' yet, this returns a gorgeous,
        // print-ready document window utilizing your AITO logo template which pops open a save prompt.
        return view('admin.exports.single-tender', compact('tender', 'categories'));
    }

    /**
     * Show the form for creating a new tender.
     */
    public function create()
    {
        return view('admin.tenders.create');
    }

    /**
     * Store a newly created tender in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'tender_ref_number' => 'required|string|unique:tenders,tender_ref_number',
            'required_grade' => 'required|array',
            'required_services' => 'required|string',
            'deadline' => 'required|date',
            'description' => 'required|string',
            'estimated_budget' => 'nullable|numeric',
            'priority_level' => 'required|string',
            'years_experience_required' => 'nullable|integer',
            'site_location' => 'nullable|string',
            'site_visit_date' => 'nullable|date',
        ]);

        // Format the data
        $validated['required_grade'] = implode(',', $request->required_grade);
        $validated['status'] = 'open';

        // This will now work because $validated contains all the new fields!
        Tender::create($validated);

        return redirect()->route('admin.tenders.index')->with('success', 'Tender published successfully!');
    }

    /**
     * Use AI to match subcontractors to a specific tender.
     */
    public function match(int $id)
    {
        $tenders = Tender::findOrFail($id);

        // Convert the tender's grade string (e.g., "G1,G2") into an array
        $requiredGradesArray = explode(',', $tenders->required_grade);

        // 1. Fetch subcons that match the required grades

        $matchedSubcons = User::query()
            ->where('role', 'subcon')
            ->where('status', 'active')
            ->where(function($query) use ($requiredGradesArray) {
                foreach ($requiredGradesArray as $cidb_grades) {
                    $query->orWhere('cidb_grades', 'LIKE', '%' . trim($cidb_grades) . '%');
                }
            })
            ->get();

        if ($matchedSubcons->isEmpty()) {
            return view('admin.tenders.match-results', [
                'tenders' => $tenders,
                'matchedSubcons' => collect(), // Pass an empty collection to avoid errors
                'aiResponse' => "No active subcontractors found matching the required grades: {$tenders->required_grade}."
            ]);
        }

        // 3. Prepare the list for AI
        $subconList = $matchedSubcons->map(function($s) {
            $name = preg_replace('/[^A-Za-z0-9 ]/', '', $s->company_name);
            $grade = is_array($s->cidb_grades) ? implode(', ', $s->cidb_grades) : $s->cidb_grades;
            $services = is_array($s->services_provided) ? implode(', ', $s->services_provided) : $s->services_provided;
            return "Company: {$name} | Subcon Grade: {$grade} | Services: {$services}";
        })->implode("\n");

        $cleanTitle = preg_replace('/[^A-Za-z0-9 ]/', '', $tenders->title);

        $prompt = "STRICT INSTRUCTION: Use ONLY the subcontractors listed below.
                TENDER PROJECT: {$cleanTitle}
                TENDER GRADES ACCEPTED: {$tenders->required_grade}
                TENDER SERVICES NEEDED: {$tenders->required_services}
                TENDER SCOPE: {$tenders->description}

                DATABASE LIST OF ELIGIBLE SUBCONTRACTORS:
                {$subconList}

                MATCHING RULES:
                1. A subcontractor is eligible if their Grade matches ANY of the TENDER GRADES.
                2. Rank the top 3 matches primarily based on SERVICE RELEVANCE and SCOPE OF WORK.

                OUTPUT FORMAT:
                - Rank 1: [Company Name]
                - Matching Logic: [Why they fit]
                - Risk/Note: [Concerns]";

        try {
            $result = Gemini::generativeModel('gemini-3.1-pro-preview')->generateContent($prompt);
            if (!empty($result->candidates) && !empty($result->candidates[0]->content->parts)) {
                $aiResponse = $result->candidates[0]->content->parts[0]->text;
            } else {
                $aiResponse = $result->text();
            }
        } catch (\Exception $e) {
            Log::error('Gemini AI Error Full: ' . json_encode([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'class' => get_class($e),
            ]));

            $aiResponse = "🚨 ERROR: " . $e->getMessage() . " | Code: " . $e->getCode() . " | Class: " . get_class($e);        }

        return view('admin.tenders.match-results', compact('tenders', 'aiResponse', 'matchedSubcons'));
    }

    /**
     * Assign the project to the chosen subcontractor.
     */
    public function assignSubcon(Request $request, Tender $tender)
    {
        $request->validate([
            'subcon_id' => 'required|exists:users,id',
        ]);

        if ($tender->selected_subcon_id && $tender->selected_subcon_id != $request->subcon_id) {

            $tender->update([
                'selected_subcon_id' => $request->subcon_id,
                'work_status' => 'assigned', // Back to the start
                'progress_percent' => 0,     // Fresh start
                'report_path' => null,       // Remove old reports
            ]);

            return redirect()->route('admin.tenders.show', $tender)
                ->with('success', 'Project successfully reassigned to a new partner.');
        }

        $tender->update([
            'selected_subcon_id' => $request->subcon_id,
            'work_status' => 'assigned',
        ]);

        return redirect()->route('admin.tenders.show', $tender);
    }

    public function edit(int $id)
    {
        $tenders = Tender::findOrFail($id);
        return view('admin.tenders.edit', compact('tenders'));

    }

    public function update(Request $request, int $id)
    {
        $tenders = Tender::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'tender_ref_number' => 'required|string',
            'required_grade' => 'required|array',
            'required_services' => 'required|string',
            'deadline' => 'required|date',
            'description' => 'required|string',
            'estimated_budget' => 'nullable|numeric',
            'priority_level' => 'required|string',
            'years_experience_required' => 'nullable|integer',
            'site_location' => 'nullable|string',
            'site_visit_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $validated['required_grade'] = implode(',', $request->required_grade);

        $tenders->update($validated);

        return redirect()->route('admin.tenders.index')->with('success', 'Tender updated successfully!');
    }

    public function destroy(int $id)
    {
        Tender::findOrFail($id)->delete();
        return redirect()->route('admin.tenders.index')->with('success', 'Tender deleted.');
    }

    public function approveReport(Tender $tenders)
    {
        // Force progress to 100 on approval
        $tenders->update([
            'work_status' => 'completed',
            'progress_percent' => 100,
            // Optional: keep the report_path so you can still see the files in the "Completed" project view
        ]);

        return redirect()->route('admin.tenders.index')->with('success', 'Project marked as Completed!');
    }

    public function show(Tender $tender)
    {
        $subcons = User::query()
            ->where('role', 'subcon')
            ->where('status', 'active')
            ->get();

        return view('admin.tenders.show', compact('tender', 'subcons'));
    }

    public function rejectFile(Request $request, Tender $tenders)
    {
        $request->validate([
            'category' => 'required|string',
            'file_index' => 'required|integer',
            'feedback' => 'required|string',
        ]);

        $reportData = $tenders->report_path;


        if (is_string($reportData)) {
            $reportData = json_decode($reportData, true);
        }

        if (!is_array($reportData) || !isset($reportData['files'])) {
            return back()->with('error', 'Critical Error: This tender uses an old file format that cannot be rejected. Please ask the subcontractor to re-upload.');
        }

        $category = $request->category;
        $index = $request->file_index;

        // Check if the specific file exists
        if (isset($reportData['files'][$category][$index])) {

            // Update the array
            $reportData['files'][$category][$index]['status'] = 'rejected';
            $reportData['files'][$category][$index]['feedback'] = $request->feedback;

            // Save it back (Laravel handles the encoding if cast is in Model)
            $tenders->update([
                'report_path' => $reportData
            ]);

            return back()->with('success', 'File rejected successfully.');
        }

        return back()->with('error', 'File index not found in category.');
    }

    public function reassign(Request $request, int $id)
    {
        $request->validate([
            'new_subcon_id' => 'required|exists:users,id',
        ]);

        $tenders = \App\Models\Tender::findOrFail($id);

        $tenders->update([
            'selected_subcon_id' => $request->new_subcon_id,
            'work_status' => 'assigned',
        ]);

        // Return a redirect so the browser reloads properly
        return redirect()->back()->with('success', 'Project reassigned successfully!');
    }
}

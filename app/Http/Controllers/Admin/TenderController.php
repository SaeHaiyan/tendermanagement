<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use App\Models\User;
use Illuminate\Http\Request;
use Gemini;
use Illuminate\Support\Facades\Log;

class TenderController extends Controller
{
    /**
     * Display a listing of the tenders.
     */
    public function index()
    {
        $tenders = Tender::latest()->get();
        return view('admin.tenders.index', compact('tenders'));
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
            'tender_ref_number' => 'required|string|unique:tenders,tender_ref_number', // Ensure this is here
            'cidb_grade' => 'required|array',
            'required_services' => 'required|string',
            'deadline' => 'required|date',
            'description' => 'required|string',
            'estimated_budget' => 'nullable|numeric', // Add this
            'priority_level' => 'required|string',    // Add this
            'years_experience_required' => 'nullable|integer', // Add this
            'site_location' => 'nullable|string',     // Add this
            'site_visit_date' => 'nullable|date',     // Add this
        ]);

        // Format the data
        $validated['cidb_grade'] = implode(',', $request->cidb_grade);
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
        $tender = Tender::findOrFail($id);

        // Convert the tender's grade string (e.g., "G1,G2") into an array
        $requiredGradesArray = explode(',', $tender->cidb_grades);

        // 1. Fetch subcons that match the required grades

        $matchedSubcons = User::where('role', 'subcon')
            ->where('status', 'active')
            ->where(function($query) use ($requiredGradesArray) {
                foreach ($requiredGradesArray as $cidb_grades) {
                    $query->orWhere('cidb_grades', 'LIKE', '%' . trim($cidb_grades) . '%');
                }
            })
            ->get();

        if ($matchedSubcons->isEmpty()) {
            return view('admin.tenders.match-results', [
                'tender' => $tender,
                'matchedSubcons' => collect(), // Pass an empty collection to avoid errors
                'aiResponse' => "No active subcontractors found matching the required grades: {$tender->cidb_grade}."
            ]);
        }

        // 3. Prepare the list for AI
        $subconList = $matchedSubcons->map(function($s) {
            $name = preg_replace('/[^A-Za-z0-9 ]/', '', $s->company_name);
            $grade = is_array($s->grade) ? implode(', ', $s->grade) : $s->grade;
            $services = is_array($s->services) ? implode(', ', $s->services) : $s->services;
            return "Company: {$name} | Subcon Grade: {$grade} | Services: {$services}";
        })->implode("\n");

        $cleanTitle = preg_replace('/[^A-Za-z0-9 ]/', '', $tender->title);

        $prompt = "STRICT INSTRUCTION: Use ONLY the subcontractors listed below.
                TENDER PROJECT: {$cleanTitle}
                TENDER GRADES ACCEPTED: {$tender->cidb_grade}
                TENDER SERVICES NEEDED: {$tender->required_services}
                TENDER SCOPE: {$tender->description}

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
            $apiKey = config('gemini.api_key');
            $client = Gemini::client($apiKey);
            $result = $client->generativeModel(model: 'gemini-3-flash-preview')->generateContent($prompt);
            $aiResponse = $result->text();
        } catch (\Exception $e) {
            // ADD THIS LINE to log the exact error to storage/logs/laravel.log
            Log::error('Gemini AI Error: ' . $e->getMessage());

            $aiResponse = "🚨 AI Analysis currently unavailable. Please review matched subcontractors manually below.";
        }

        return view('admin.tenders.match-results', compact('tender', 'aiResponse', 'matchedSubcons'));
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

            return redirect()->route('admin.tenders.show', $tender->id)
                ->with('success', 'Project successfully reassigned to a new partner.');
        }

        $tender->update([
            'selected_subcon_id' => $request->subcon_id,
            'work_status' => 'assigned',
        ]);

        return redirect()->route('admin.tenders.show', $tender->id)
            ->with('success', 'Partner assigned successfully.');
    }

    public function edit(int $id)
    {
        $tender = Tender::findOrFail($id);
        return view('admin.tenders.edit', compact('tender'));

    }

    public function update(Request $request, int $id)
    {
        $tender = Tender::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'tender_ref_number' => 'required|string',
            'cidb_grade' => 'required|array',
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

        $validated['cidb_grade'] = implode(',', $request->cidb_grade);

        $tender->update($validated);

        return redirect()->route('admin.tenders.index')->with('success', 'Tender updated successfully!');
    }

    public function destroy(int $id)
    {
        Tender::findOrFail($id)->delete();
        return redirect()->route('admin.tenders.index')->with('success', 'Tender deleted.');
    }

    public function approveReport(Tender $tender)
    {
        // Force progress to 100 on approval
        $tender->update([
            'work_status' => 'completed',
            'progress_percent' => 100,
            // Optional: keep the report_path so you can still see the files in the "Completed" project view
        ]);

        return redirect()->route('admin.tenders.index')->with('success', 'Project marked as Completed!');
    }

    public function show(Tender $tender)
    {
        $subcons = User::where('role', 'subcon')->get();

        return view('admin.tenders.show', compact('tender', 'subcons'));
    }

    public function rejectFile(Request $request, Tender $tender)
    {
        $request->validate([
            'category' => 'required|string',
            'file_index' => 'required|integer',
            'feedback' => 'required|string',
        ]);

        $reportData = $tender->report_path;


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
            $tender->update([
                'report_path' => $reportData
            ]);

            return back()->with('success', 'File rejected successfully.');
        }

        return back()->with('error', 'File index not found in category.');
    }

    public function reassign(Request $request, $id)
    {
        $request->validate([
            'new_subcon_id' => 'required|exists:users,id',
        ]);

        $tender = \App\Models\Tender::findOrFail($id);

        $tender->update([
            'selected_subcon_id' => $request->new_subcon_id,
            'work_status' => 'assigned',
        ]);

        // Return a redirect so the browser reloads properly
        return redirect()->back()->with('success', 'Project reassigned successfully!');
    }
}

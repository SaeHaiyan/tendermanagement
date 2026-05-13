<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use App\Models\User;
use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class TenderController extends Controller
{
    /**
     * Display a listing of the tenders.
     */
    public function index()
    {
        $tenders = Tender::latest('created_at')->get();
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
            'tender_ref_number' => 'required|string|unique:tenders,tender_ref_number',
            'required_grade' => 'required|array',
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
            $result = Gemini::generativeModel('gemini-2.0-flash')->generateContent($prompt);
            // Try to get the text from the response
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

            $aiResponse = "🚨 AI Analysis currently unavailable. Please review matched subcontractors manually below.";
        }

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

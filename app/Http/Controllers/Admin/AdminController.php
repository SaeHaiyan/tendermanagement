<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tender;
use Illuminate\View\View;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(): View
    {
        $users = User::all();
        $projects = Tender::with('selectedSubcon')->latest()->get();

        return view('admin.dashboard', compact('users', 'projects'));
    }

    /**
     * Show Subcontractor Profile
     */
    public function show(int $id)
    {
        $subcon = User::findOrFail($id);
        return view('admin.show', compact('subcon'));
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

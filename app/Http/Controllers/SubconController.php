<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Tender;

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

        return view('subcon.dashboard', compact('activeProjects', 'completedProjects'));
    }

    public function uploadPendingDocuments(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'documents' => 'required|array|min:1',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120',
        ]);

        $pendingDocuments = $user->pending_documents ?? [];

        foreach ($request->file('documents') as $file) {
            $path = $file->store('pending-account-documents/' . $user->id, 'public');
            $pendingDocuments[] = [
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'status' => 'pending',
                'uploaded_at' => now()->toDateTimeString(),
            ];
        }

        $user->pending_documents = $pendingDocuments;
        $user->save();

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

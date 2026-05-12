<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Added for file deletion
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

    // 1. FOR NEW UPLOADS (Grouped by Category)
    public function updateProgress(Request $request, Tender $tender)
    {
        $request->validate([
            'category_type' => 'required|string',
            'documents' => 'required|array',
            'documents.*' => 'mimes:pdf,jpg,png|max:10240',
        ]);

        // Start with existing data or empty structure
        $reportData = $tender->report_path ?? ['files' => []];
        $category = $request->category_type;

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('reports', 'public');

                $reportData['files'][$category][] = [
                    'path' => $path,
                    'status' => 'pending',
                    'feedback' => null,
                    'uploaded_at' => now()->toDateTimeString()
                ];
            }
        }

        $tender->report_path = $reportData;
        $tender->save();

        return back()->with('success', 'Files uploaded successfully!');
    }

    // 2. THE MISSING LINK: REPLACE REJECTED FILE
    public function replaceFile(Request $request, Tender $tender)
    {
        $request->validate([
            'new_file' => 'required|mimes:pdf,jpg,png|max:10240',
            'category' => 'required|string',
            'file_index' => 'required|integer',
        ]);

        $reportData = $tender->report_path;
        $category = $request->category;
        $index = $request->file_index;

        if (isset($reportData['files'][$category][$index])) {
            // Delete old file from storage
            Storage::disk('public')->delete($reportData['files'][$category][$index]['path']);

            // Store new file
            $path = $request->file('new_file')->store('reports', 'public');

            // Update array
            $reportData['files'][$category][$index] = [
                'path' => $path,
                'status' => 'pending',
                'feedback' => null,
                'uploaded_at' => now()->toDateTimeString()
            ];

            $tender->report_path = $reportData;
            $tender->save();

            return back()->with('success', 'File replaced successfully!');
        }

        return back()->with('error', 'Could not find the file to replace.');
    }

        public function approve(int $id)
        {
            // Find the tender by ID
            $tender = \App\Models\Tender::findOrFail($id);

            // Update status to completed and progress to 100%
            $tender->update([
                'work_status' => 'completed',
                'progress_percent' => 100
            ]);

            return redirect()->back()->with('success', 'Project approved and marked as completed!');
        }
}

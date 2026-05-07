<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(): View
    {
        $users = User::all();

        return view('admin.dashboard', compact('users'));
    }

    public function show( int $id)
    {
        $subcon = User::findOrFail($id);
        return view('admin.show', compact('subcon'));
    }

    public function updateStatus(Request $request, int $id)
    {

        $subcon = \App\Models\User::findOrFail($id);

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
}

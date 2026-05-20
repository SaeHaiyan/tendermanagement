<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\TenderController;
use App\Http\Controllers\SubconController;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('subcon.dashboard');
    }
    return redirect()->route('login');
});

// --- NEW SECURITY ROUTE: FORCED TAB LOGOUT ---
Route::get('/logout-forced', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout-forced');

// --- SUBCON DASHBOARD & PROGRESS ROUTES ---
Route::middleware(['auth', 'check_status'])->group(function () {
    Route::get('/subcon/dashboard', [SubconController::class, 'index'])->name('subcon.dashboard');
    Route::post('/subcon/pending-documents', [SubconController::class, 'uploadPendingDocuments'])->name('subcon.pending-documents.upload');
    Route::get('/subcon/documents', [SubconController::class, 'documents'])->name('subcon.documents.index');
    Route::post('/subcon/documents', [SubconController::class, 'uploadDocuments'])->name('subcon.documents.upload');
    Route::get('/subcon/tenders/{project}/manage', [SubconController::class, 'manage'])->name('subcon.tenders.manage');
    Route::get('/subcon/dashboard/history', [SubconController::class, 'history'])->name('subcon.dashboard.history');

    Route::patch('/subcon/dashboard/tenders/{tender}/progress', [SubconController::class, 'updateProgress'])->name('subcon.tenders.update-progress');
    Route::post('/subcon/dashboard/tenders/{tender}/upload-report', [SubconController::class, 'uploadReport'])->name('subcon.tenders.upload-report');
    Route::post('/tenders/{tender}/replace-file', [SubconController::class, 'replaceFile'])->name('subcon.tenders.replace-file');
});

// --- PROFILE ROUTES ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- ADMIN MANAGEMENT ROUTES ---
Route::middleware(['auth', CheckAdmin::class])->group(function () {

    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/pending-approvals', [AdminController::class, 'pendingApprovals'])->name('admin.pending-approvals');
    Route::get('/admin/activity', [AdminController::class, 'activity'])->name('admin.activity');
    Route::get('/admin/dashboard/export', [AdminController::class, 'exportUsers'])->name('admin.dashboard.export');
    Route::get('/admin/subcon/{id}', [AdminController::class, 'show'])->name('admin.subcon.show');
    Route::patch('/admin/subcon/{id}/status', [AdminController::class, 'updateStatus'])->name('admin.subcon.update-status');
    Route::delete('/admin/subcon/{id}', [AdminController::class, 'destroy'])->name('admin.subcon.destroy');

    Route::get('/admin/tenders', [TenderController::class, 'index'])->name('admin.tenders.index');
    Route::get('/admin/tenders/export', [TenderController::class, 'export'])->name('admin.tenders.export');
    Route::get('/admin/tenders/create', [TenderController::class, 'create'])->name('admin.tenders.create');
    Route::post('/admin/tenders', [TenderController::class, 'store'])->name('admin.tenders.store');
    Route::get('/admin/tenders/{tender}/edit', [TenderController::class, 'edit'])->name('admin.tenders.edit');
    Route::put('/admin/tenders/{tender}', [TenderController::class, 'update'])->name('admin.tenders.update');
    Route::delete('/admin/tenders/{tender}', [TenderController::class, 'destroy'])->name('admin.tenders.destroy');

    Route::get('/admin/tenders/{tender}/match', [TenderController::class, 'match'])->name('admin.tenders.match');
    Route::patch('/admin/tenders/{tender}/assign', [TenderController::class, 'assignSubcon'])->name('admin.tenders.assign');
    Route::patch('/admin/tenders/{id}/reassign', [TenderController::class, 'reassign'])->name('admin.tenders.reassign');
    Route::get('/admin/tenders/{tender}/export-single', [TenderController::class, 'exportSingle'])->name('admin.tenders.export-single');
    Route::get('/admin/tenders/{tender}', [TenderController::class, 'show'])->name('admin.tenders.show');

    Route::patch('/admin/tenders/{id}/approve', [SubconController::class, 'approve'])->name('admin.tenders.approve');
    Route::post('/admin/tenders/{tender}/reject-file', [TenderController::class, 'rejectFile'])->name('admin.tenders.reject-file');
});

require __DIR__.'/auth.php';

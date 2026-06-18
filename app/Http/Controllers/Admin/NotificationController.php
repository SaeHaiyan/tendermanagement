<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $svc;

    public function __construct(AdminNotificationService $svc)
    {
        $this->svc = $svc;
    }

    public function index()
    {
        $items = collect($this->svc->all())->sortByDesc('created_at')->values();
        return view('admin.notifications.index', ['items' => $items]);
    }

    public function markRead(Request $request, $id)
    {
        $this->svc->markRead($id);
        if ($request->wantsJson()) return response()->json(['ok' => true]);
        return back();
    }

    public function markAllRead(Request $request)
    {
        $this->svc->markAllRead();
        if ($request->wantsJson()) return response()->json(['ok' => true]);
        return back();
    }
}

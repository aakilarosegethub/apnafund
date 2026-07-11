<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminActivityLog::with(['admin', 'role'])
            ->orderByDesc('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }
        if ($request->filled('module_name')) {
            $query->where('module_name', $request->module_name);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logs = $query->paginate(20)->withQueryString();

        $actionTypes = AdminActivityLog::select('action_type')
            ->distinct()
            ->orderBy('action_type')
            ->pluck('action_type');
        $modules = AdminActivityLog::select('module_name')
            ->distinct()
            ->whereNotNull('module_name')
            ->orderBy('module_name')
            ->pluck('module_name');

        return view('admin.activity_logs.index', compact('logs', 'actionTypes', 'modules'));
    }

    public function show(AdminActivityLog $activity_log)
    {
        $activity_log->load(['admin', 'role']);

        return response()->json([
            'log' => $activity_log,
            'admin_name' => $activity_log->admin?->name ?? 'System',
        ]);
    }
}

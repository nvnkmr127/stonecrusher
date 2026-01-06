<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('view-audit-logs')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Activity::with(['causer', 'subject'])->latest();

        if ($request->filled('description')) {
            $query->where('description', 'like', "%{$request->description}%");
        }

        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id);
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->paginate(20)->withQueryString();

        $users = \App\Models\User::orderBy('name')->get(['id', 'name']);

        // efficient way to get used subject types?
        // simple approach: hardcoded common ones for now to avoid slow distinct query on large table
        // or just group by subject_type
        $subjectTypes = Activity::select('subject_type')->distinct()->pluck('subject_type')->map(function ($type) {
            return [
                'value' => $type,
                'label' => class_basename($type)
            ];
        })->sortBy('label');

        return view('admin.audit-logs.index', compact('activities', 'users', 'subjectTypes'));
    }
}

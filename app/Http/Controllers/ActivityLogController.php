<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Display a paginated, filterable audit trail of every logged model
     * change across the system. Admin-only.
     */
    public function index(Request $request): View
    {
        $query = Activity::with(['causer', 'subject'])->latest();

        if ($subjectType = $request->string('subject_type')->toString()) {
            $query->where('subject_type', $subjectType);
        }

        if ($event = $request->string('event')->toString()) {
            $query->where('event', $event);
        }

        $activities = $query->paginate(25)->withQueryString();

        $subjectTypes = Activity::query()
            ->select('subject_type')
            ->distinct()
            ->whereNotNull('subject_type')
            ->orderBy('subject_type')
            ->pluck('subject_type');

        return view('activity-log.index', [
            'activities' => $activities,
            'subjectTypes' => $subjectTypes,
            'filters' => $request->only(['subject_type', 'event']),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Navigation\NavigationItem;
use Illuminate\Http\Request;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'event' => ['nullable', 'string'],
            'subject_type' => ['nullable', 'string'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $activities = Activity::query()
            ->where('school_id', $request->school()->id)
            ->when($filters['event'] ?? null, fn ($query, $event) => $query->where('event', $event))
            ->when($filters['subject_type'] ?? null, fn ($query, $type) => $query->where('subject_type', $type))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->where('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->where('created_at', '<', now()->parse($to)->addDay()))
            ->with(['causer', 'subject'])
            ->latest('id')
            ->paginate()
            ->withQueryString();

        return inertia('activity/Index', [
            'title' => __('Activity'),
            'activities' => ActivityResource::collection($activities),
            'filters' => $filters,
            'events' => Activity::query()->where('school_id', $request->school()->id)->whereNotNull('event')->distinct()->orderBy('event')->pluck('event'),
            'subjectTypes' => Activity::query()->where('school_id', $request->school()->id)->whereNotNull('subject_type')->distinct()->orderBy('subject_type')->pluck('subject_type'),
            'breadcrumbs' => $this->withBreadcrumbs(
                NavigationItem::make()
                    ->to(route('activity.index'))
                    ->isCurrent()
                    ->labeled(__('Activity')),
            ),
        ]);
    }
}

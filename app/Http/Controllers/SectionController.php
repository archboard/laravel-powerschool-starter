<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Http\Resources\SectionResource;
use App\Models\School;
use App\Models\Section;
use App\Navigation\NavigationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Response;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, School $school): Response
    {
        $this->authorize(Permission::viewAny, Section::class);

        $sections = $school->sections()
            ->filter($request->currentFilters())
            ->with('course', 'teacher')
            ->withCount('students')
            ->join('courses', 'courses.id', '=', 'sections.course_id')
            ->orderBy('courses.name')
            ->orderBy('sections.section_number')
            ->paginate(25);
        $title = __('Sections');

        return inertia('sections/Index', [
            'title' => $title,
            'sections' => SectionResource::collection($sections),
            'model_alias' => Str::toModelAlias(Section::class),
        ])->withViewData(compact('title'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section): Response
    {
        $this->authorize(Permission::view, $section);

        $section->load('course', 'teacher', 'students');
        $title = __('Section :number of :course_name', [
            'number' => $section->section_number,
            'course_name' => $section->course->name,
        ]);

        return inertia('sections/Show', [
            'title' => $title,
            'section' => new SectionResource($section),
            'breadcrumbs' => $this->withBreadcrumbs(
                NavigationItem::make()
                    ->to(route('sections.index'))
                    ->labeled(__('Sections')),
                NavigationItem::make()
                    ->to(route('sections.show', $section))
                    ->labeled($section->display)
                    ->isCurrent(),
            ),
        ])->withViewData(compact('title'));
    }
}

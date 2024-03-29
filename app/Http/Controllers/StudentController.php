<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\School;
use App\Models\Student;
use App\Navigation\NavigationItem;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, School $school)
    {
        $title = __('Students');
        $students = $school->students()
            ->filter($request->all())
            ->paginate(25);

        return inertia('students/Index', [
            'title' => $title,
            'students' => StudentResource::collection($students),
            'breadcrumbs' => $this->withBreadcrumbs(
                NavigationItem::make()
                    ->labeled(__('Students'))
                    ->to(route('students.index'))
                    ->isCurrent()
            ),
        ])->withViewData(compact('title'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $title = $student->name;
        $student->load('school', 'sections', 'sections.course');

        return inertia('students/Show', [
            'title' => $title,
            'student' => new StudentResource($student),
            'breadcrumbs' => $this->withBreadcrumbs(
                NavigationItem::make()
                    ->labeled(__('Students'))
                    ->to(route('students.index')),
                NavigationItem::make()
                    ->labeled($student->name)
                    ->to(route('students.show', $student))
                    ->isCurrent(),
            ),
        ])->withViewData(compact('title'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        //
    }
}

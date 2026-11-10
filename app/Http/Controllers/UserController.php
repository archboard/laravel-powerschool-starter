<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Http\Resources\UserResource;
use App\Models\School;
use App\Models\User;
use App\Navigation\NavigationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, School $school): Response
    {
        $this->authorize(Permission::viewAny, User::class);

        $title = __('Users');
        $filters = $request->currentFilters();
        $users = $school->users()
            ->filter($filters)
            ->paginate(25);

        return inertia('users/Index', [
            'title' => $title,
            'users' => UserResource::collection($users),
            'availableFilters' => (new User)->availableFiltersToArray(),
            'currentFilters' => (object) $filters,
            'model_alias' => Str::toModelAlias(User::class),
            'breadcrumbs' => $this->withBreadcrumbs(
                NavigationItem::make()
                    ->labeled(__('Users'))
                    ->to(route('users.index'))
                    ->isCurrent()
            ),
        ])->withViewData(compact('title'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): Response
    {
        $this->authorize(Permission::view, $user);

        $title = $user->name;

        $user->load('students');

        return inertia('users/Show', [
            'title' => $title,
            'subjectUser' => new UserResource($user),
        ])->withViewData(compact('title'));
    }
}

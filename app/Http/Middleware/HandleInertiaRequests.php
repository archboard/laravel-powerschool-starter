<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use App\Http\Resources\SchoolResource;
use App\Http\Resources\UserResource;
use App\Models\School;
use App\Models\User;
use App\Navigation\NavigationItem;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'layouts.app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        /** @var User|null $user */
        $user = $request->user();
        $tenant = $request->tenant();

        return array_merge(parent::share($request), [
            'user' => function () use ($user) {
                if ($user) {
                    return new UserResource($user);
                }

                return new \stdClass();
            },
            'school' => fn () => new SchoolResource(app(School::class)),
            'adminSchools' => function () use ($user, $tenant) {
                if (!$user) {
                    return [];
                }

                $schools = $user->isA(Role::DISTRICT_ADMIN->value)
                    ? $tenant->schools()
                        ->active()
                        ->orderBy('name')
                        ->get()
                    : $user->adminSchools()
                        ->get();

                return SchoolResource::collection($schools);
            },
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
            'navigation' => function () use ($user, $request): array {
                if (!$user) {
                    return [];
                }

                $nav = [
                    NavigationItem::make()
                        ->labeled(__('Dashboard'))
                        ->to('/')
                        ->isCurrent($request->is('/'))
                        ->withIcon('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>'),

                    NavigationItem::make()
                        ->labeled(__('Students'))
                        ->to('/students')
                        ->isCurrent($request->routeIs('students.*'))
                        ->withIcon('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" /></svg>'),
                ];

                return array_map(fn (NavigationItem $item) => $item->toArray(), $nav);
            },
            'secondaryNav' => function () use ($user, $request): array {
                if (!$user) {
                    return [];
                }

                $nav = [
                    NavigationItem::make()
                        ->labeled(__('Personal settings'))
                        ->to(route('settings.personal.edit')),
                ];

                if ($user->can('edit school settings') && $request->school()) {
                    $nav[] = NavigationItem::make()
                        ->labeled(__('School settings'))
                        ->to(route('settings.school.edit'));
                }

                if ($user->can('edit tenant settings')) {
                    $nav[] = NavigationItem::make()
                        ->labeled(__('Tenant settings'))
                        ->isCurrent($request->routeIs('settings.tenant.edit'))
                        ->to(route('settings.tenant.edit'));
                }

                $nav[] = NavigationItem::make()
                    ->labeled(__('Sign out'))
                    ->to(url('/logout'))
                    ->asButton()
                    ->method('post');

                return array_map(fn (NavigationItem $item) => $item->toArray(), $nav);
            },
        ]);
    }
}

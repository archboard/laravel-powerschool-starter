<?php

namespace App\Http\Middleware;

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
        return array_merge(parent::share($request), [
            'user' => function () use ($user) {
                if ($user) {
                    return new UserResource($user);
                }

                return new \stdClass();
            },
            'school' => function () {
                return new SchoolResource(app(School::class));
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
                        ->withIcon('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>')
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

                if ($user->can('edit school settings')) {
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

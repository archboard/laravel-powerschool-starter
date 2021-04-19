<?php

namespace App\Providers;

use App\Models\School;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();

        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $currentTenant = function (): Tenant {
            /** @var Tenant $current */
            $current = Tenant::current();

            return $current ?? new Tenant();
        };

        $currentSchool = function (): School {
            /** @var User $user */
            $user = auth()->user();

            if ($user && $school = $user->school) {
                return $school;
            }

            return new School();
        };

        if (!$this->app->runningInConsole()) {
            $this->app->bind(Tenant::class, $currentTenant);
            $this->app->bind(School::class, $currentSchool);
        }

        Request::macro('tenant', $currentTenant);
        Request::macro('school', $currentSchool);
    }
}

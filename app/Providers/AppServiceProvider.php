<?php

namespace App\Providers;

use App\Models\School;
use App\Models\Tenant;
use App\Models\User;
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

        if (!$this->app->runningInConsole()) {
            $this->app->bind(Tenant::class, function () {
                return Tenant::current() ?? new Tenant();
            });

            $this->app->bind(School::class, function () {
                /** @var User $user */
                $user = auth()->user();

                if ($user && $school = $user->school) {
                    return $school;
                }

                return new School();
            });
        }
    }
}

<?php

namespace App\Providers;

use App\Models\School;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
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

        $currentTenant = fn (): Tenant => Tenant::current() ?? new Tenant();

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

        Relation::morphMap([
            'user' => \App\Models\User::class,
            'student' => \App\Models\Student::class,
            'tenant' => \App\Models\Tenant::class,
            'school' => \App\Models\School::class,
            'section' => \App\Models\Section::class,
        ]);
    }
}

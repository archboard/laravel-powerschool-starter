<?php

namespace App\Providers;

use App\Enums\UserType;
use App\Models\Course;
use App\Models\School;
use App\Models\Section;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Carbon\CarbonImmutable;
use GrantHolle\PowerSchool\Auth\UserFactory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
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
        Date::use(CarbonImmutable::class);

        $currentTenant = fn (): Tenant => Tenant::current() ?? new Tenant;

        $currentSchool = function (): School {
            /** @var User|null $user */
            $user = auth()->user();

            if ($user !== null && $school = $user->school) {
                return $school;
            }

            return new School;
        };

        $this->app->bind(Tenant::class, $currentTenant);
        $this->app->bind(School::class, $currentSchool);

        Request::macro('tenant', $currentTenant);
        Request::macro('school', $currentSchool);

        Request::macro('currentFilters', function () {
            /** @var Request $this */
            return $this->collect('f')
                ->mapWithKeys(fn (array $filter, $key) => [$key => [
                    'key' => $filter['key'],
                    'operator' => $filter['operator'] ?? null,
                    'value' => $filter['value'] ?? null,
                ]]);
        });

        Request::macro('addFilter', function (string|array $key, mixed $value = null) {
            /** @var Request $this */
            $data = $this->all();
            $filters = $data['f'] ?? [];
            $filters[$key] = [
                'key' => $key,
                'value' => $value,
            ];
            $data['f'] = $filters;
            $this->merge($data);

            return $this;
        });

        Relation::morphMap([
            'user' => User::class,
            'student' => Student::class,
            'tenant' => Tenant::class,
            'school' => School::class,
            'section' => Section::class,
            'course' => Course::class,
        ]);

        // Add the tenant_id to the identifying attributes when looking up a user
        UserFactory::findUserUsing(function (Collection $data, string $model, array $attributes) {
            /** @var Tenant $tenant */
            $tenant = Tenant::current();
            $userType = UserType::fromData($data);

            $user = $model::firstOrNew([
                ...$attributes,
                'tenant_id' => $tenant->id,
                'sis_key' => $userType->getSisKeyFromData($data),
            ]);
            $user->user_type = $userType;

            return $user;
        });
    }
}

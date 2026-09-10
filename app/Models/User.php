<?php

namespace App\Models;

use App\Enums\Role;
use App\Enums\UserType;
use App\Models\Contracts\ExistsInSis;
use App\Services\Filters\BaseFilter;
use App\Services\Filters\Enums\Component;
use App\Services\Filters\MultipleSelectFilter;
use App\Services\Filters\TextFilter;
use App\Traits\BelongsToTenant;
use App\Traits\HasFilters;
use App\Traits\HasFirstAndLastName;
use App\Traits\HasPermissions;
use App\Traits\HasTimezone;
use App\Traits\Selectable;
use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\HasRolesAndAbilities;

/**
 * @property int $id
 * @property int $tenant_id
 * @property int|null $sis_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $password
 * @property int|null $school_id
 * @property string|null $timezone
 * @property string|null $remember_token
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string $sis_key
 * @property UserType|null $user_type
 * @property string $locale
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Ability> $abilities
 * @property-read int|null $abilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, School> $adminSchools
 * @property-read int|null $admin_schools_count
 * @property-read mixed $last_first
 * @property-read mixed $name
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read mixed $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Silber\Bouncer\Database\Role> $roles
 * @property-read int|null $roles_count
 * @property-read School|null $school
 * @property-read \Illuminate\Database\Eloquent\Collection<int, School> $schools
 * @property-read int|null $schools_count
 * @property-read SelectedModel|null $selectedModel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SelectedModel> $selectedModels
 * @property-read int|null $selected_models_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Student> $students
 * @property-read int|null $students_count
 * @property-read Tenant $tenant
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User filter(\Illuminate\Support\Collection<string, mixed>|array<string, mixed> $data)
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User search(string $search)
 * @method static Builder<static>|User whereCan(string $ability)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereFirstName($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereIs($role)
 * @method static Builder<static>|User whereIsAll($role)
 * @method static Builder<static>|User whereIsNot($role)
 * @method static Builder<static>|User whereLastName($value)
 * @method static Builder<static>|User whereLocale($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereSchoolId($value)
 * @method static Builder<static>|User whereSisId($value)
 * @method static Builder<static>|User whereSisKey($value)
 * @method static Builder<static>|User whereTenantId($value)
 * @method static Builder<static>|User whereTimezone($value)
 * @method static Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static Builder<static>|User whereTwoFactorSecret($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User whereUserType($value)
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable implements ExistsInSis
{
    use BelongsToTenant;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasFilters;
    use HasFirstAndLastName;
    use HasPermissions;
    use HasRolesAndAbilities;
    use HasTimezone;
    use Notifiable;
    use Selectable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_type' => UserType::class,
    ];

    // -------------------------------------------------------------------------
    // Query scopes
    // -------------------------------------------------------------------------

    /**
     * Gets the users who have an ability directly or through a role
     */
    /**
     * @param  Builder<User>  $query
     */
    public function scopeWhereCan(Builder $query, string $ability): void
    {
        $query->where(function ($query) use ($ability) {
            // direct
            $query->whereHas('abilities', function ($query) use ($ability) {
                $query->whereIn('name', [$ability, '*']);
            });
            // through roles
            $query->orWhereHas('roles', function ($query) use ($ability) {
                $query->whereHas('abilities', function ($query) use ($ability) {
                    $query->whereIn('name', [$ability, '*']);
                });
            });
        });
    }

    /**
     * @param  Builder<User>  $builder
     */
    public function scopeSearch(Builder $builder, string $search): void
    {
        $builder->where(function (Builder $builder) use ($search) {
            $builder->where(DB::raw("(first_name || ' ' || last_name)"), 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%");
        });
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * @return BelongsToMany<School, $this>
     */
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class);
    }

    /**
     * @return BelongsToMany<Student, $this>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)
            ->withPivot(['relationship']);
    }

    /**
     * @return BelongsToMany<School, $this>
     */
    public function adminSchools(): BelongsToMany
    {
        return $this->schools()
            ->active()
            ->orderBy('name');
    }

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return HasMany<SelectedModel, $this>
     */
    public function selectedModels(): HasMany
    {
        return $this->hasMany(SelectedModel::class);
    }

    // -------------------------------------------------------------------------
    // Instance functions
    // -------------------------------------------------------------------------

    public function syncFromSis(): static
    {
        $this->tenant->getSisProvider()?->syncUser($this);

        return $this;
    }

    public function assignRole(Role|string $role): static
    {
        return $this->assign($role instanceof Role ? $role->value : $role);
    }

    /**
     * Toggles a model as selected for the user.
     */
    public function toggleSelectedModelInstance(Model $model): static
    {
        $selection = $this->selectedModels()
            ->firstOrCreate([
                'tenant_id' => $this->tenant_id,
                'school_id' => $this->school_id,
                'user_id' => $this->id,
                'selectable_type' => $model->getMorphClass(),
                'selectable_id' => $model->getKey(),
            ]);

        if (! $selection->wasRecentlyCreated) {
            $selection->delete();
        }

        return $this;
    }

    public function toggleSelectedModel(string $modelAlias, int $id): static
    {
        if (class_exists($modelAlias)) {
            /** @var Model $instance */
            $instance = new $modelAlias;
            $modelAlias = $instance->getMorphClass();
        }

        if ($model = Relation::getMorphedModel($modelAlias)) {
            return $this->toggleSelectedModelInstance(new $model(['id' => $id]));
        }

        return $this;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function selectAllModel(string $modelAlias, array $filters = []): static
    {
        if (class_exists($modelAlias)) {
            /** @var Model $instance */
            $instance = new $modelAlias;
            $modelAlias = $instance->getMorphClass();
        }

        if (Relation::getMorphedModel($modelAlias)) {
            $relationship = Str::plural($modelAlias);

            $data = $this->school
                ->$relationship()
                ->filter($filters)
                ->pluck('id')
                ->map(fn ($id) => [
                    'tenant_id' => $this->tenant_id,
                    'school_id' => $this->school_id,
                    'user_id' => $this->id,
                    'selectable_type' => $modelAlias,
                    'selectable_id' => $id,
                ]);

            $this->deselectAllModel($modelAlias)
                ->selectedModels()
                ->insert($data->toArray());
        }

        return $this;
    }

    public function deselectAllModel(string $modelAlias): static
    {
        $this->selectedModels()
            ->where('selectable_type', $modelAlias)
            ->where('school_id', $this->school_id)
            ->delete();

        return $this;
    }

    /**
     * @return Collection<int, mixed>
     */
    public function getModelSelection(string $model): Collection
    {
        $modelAlias = $model;
        if (class_exists($model)) {
            /** @var Model $instance */
            $instance = new $model;
            $modelAlias = $instance->getMorphClass();
        }

        return $this->selectedModels()
            ->where('school_id', $this->school_id)
            ->where('selectable_type', $modelAlias)
            ->pluck('selectable_id')
            ->values();
    }

    /**
     * @return array<int, BaseFilter>
     */
    public function filters(): array
    {
        return [
            TextFilter::make('search', __('Search'))
                ->hide()
                ->using(fn ($builder, string $search) => $builder->search($search)),
            TextFilter::make('first_name', __('First name')),
            TextFilter::make('last_name', __('Last name')),
            MultipleSelectFilter::make('user_type', __('Checkbox group'))
                ->options(UserType::options()),
            MultipleSelectFilter::make('user_type', __('Combobox'))
                ->withComponent(Component::combobox)
                ->options(UserType::options()),
            MultipleSelectFilter::make('user_type', __('Select'))
                ->withComponent(Component::combobox)
                ->options(UserType::options()),
        ];
    }
}

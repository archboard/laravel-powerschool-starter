<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Course
 *
 * @mixin IdeHelperCourse
 * @property int $id
 * @property int $tenant_id
 * @property int $school_id
 * @property string $name
 * @property int $sis_id
 * @property string|null $course_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCourseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereSisId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereUpdatedAt($value)
 */
	class IdeHelperCourse extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\School
 *
 * @mixin IdeHelperSchool
 * @property int $id
 * @property int $tenant_id
 * @property int $sis_id
 * @property int $school_number
 * @property string $name
 * @property int $high_grade
 * @property int $low_grade
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Course[] $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Section[] $sections
 * @property-read int|null $sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Student[] $students
 * @property-read int|null $students_count
 * @property-read \App\Models\Tenant $tenant
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|School newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|School newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|School query()
 * @method static \Illuminate\Database\Eloquent\Builder|School whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereHighGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereLowGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereSchoolNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereSisId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereUpdatedAt($value)
 */
	class IdeHelperSchool extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Section
 *
 * @mixin IdeHelperSection
 * @property int $id
 * @property int $tenant_id
 * @property int $school_id
 * @property int $course_id
 * @property int $user_id
 * @property int $sis_id
 * @property string|null $section_number
 * @property string|null $expression
 * @property string|null $external_expression
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Student[] $students
 * @property-read int|null $students_count
 * @method static \Illuminate\Database\Eloquent\Builder|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereExpression($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereExternalExpression($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereSectionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereSisId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereUserId($value)
 */
	class IdeHelperSection extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Student
 *
 * @mixin IdeHelperStudent
 * @property int $id
 * @property int $tenant_id
 * @property int $school_id
 * @property int $sis_id
 * @property string|null $student_number
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Section[] $sections
 * @property-read int|null $sections_count
 * @method static \Illuminate\Database\Eloquent\Builder|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereSisId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereStudentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereUpdatedAt($value)
 */
	class IdeHelperStudent extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Tenant
 *
 * @mixin IdeHelperTenant
 * @property int $id
 * @property string $name
 * @property string $domain
 * @property string|null $ps_url
 * @property string|null $ps_client_id
 * @property string|null $ps_secret
 * @property bool $allow_password_auth
 * @property string|null $subscription_started_at
 * @property string|null $subscription_expires_at
 * @property string $license
 * @property string $sis_provider
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\School[] $schools
 * @property-read int|null $schools_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Section[] $sections
 * @property-read int|null $sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Student[] $students
 * @property-read int|null $students_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Spatie\Multitenancy\TenantCollection|static[] all($columns = ['*'])
 * @method static \Spatie\Multitenancy\TenantCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereAllowPasswordAuth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereLicense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant wherePsClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant wherePsSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant wherePsUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereSisProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereSubscriptionExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereSubscriptionStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereUpdatedAt($value)
 */
	class IdeHelperTenant extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @mixin IdeHelperUser
 * @property int $id
 * @property int $tenant_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $email
 * @property string|null $password
 * @property int|null $school_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $timezone
 * @property int|null $sis_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Silber\Bouncer\Database\Ability[] $abilities
 * @property-read int|null $abilities_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Silber\Bouncer\Database\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\School|null $school
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\School[] $schools
 * @property-read int|null $schools_count
 * @property-read \App\Models\Tenant $tenant
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCan(string $ability)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereFirstName($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereIs($role)
 * @method static Builder|User whereIsAll($role)
 * @method static Builder|User whereIsNot($role)
 * @method static Builder|User whereLastName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereSchoolId($value)
 * @method static Builder|User whereSisId($value)
 * @method static Builder|User whereTenantId($value)
 * @method static Builder|User whereTimezone($value)
 * @method static Builder|User whereUpdatedAt($value)
 */
	class IdeHelperUser extends \Eloquent {}
}


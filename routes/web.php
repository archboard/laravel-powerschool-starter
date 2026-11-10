<?php

use App\Enums\Permission;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth\PowerSchoolOidcLoginController;
use App\Http\Controllers\Auth\PowerSchoolOpenIdLoginController;
use App\Http\Controllers\CheckAuthStatusController;
use App\Http\Controllers\ClassLinkOAuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DemoLoginController;
use App\Http\Controllers\DistrictLookupController;
use App\Http\Controllers\DownloadPowerSchoolPluginController;
use App\Http\Controllers\GetLanguagesController;
use App\Http\Controllers\GetSelectionController;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\InstallFirstUserController;
use App\Http\Controllers\RefreshCsrfTokenController;
use App\Http\Controllers\SchoolSelectionController;
use App\Http\Controllers\Search\SisUserController;
use App\Http\Controllers\SearchModelController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SendSmtpTestController;
use App\Http\Controllers\Settings\PersonalSettingsController;
use App\Http\Controllers\Settings\SchoolSettingsController;
use App\Http\Controllers\Settings\SyncModelController;
use App\Http\Controllers\Settings\SyncSchoolItemController;
use App\Http\Controllers\Settings\TenantSettingsController;
use App\Http\Controllers\StartCloudSetupController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ToggleSelectionController;
use App\Http\Controllers\UpdateCurrentSchoolController;
use App\Http\Controllers\UpdateLocaleController;
use App\Http\Controllers\UpdateSmtpSettingsController;
use App\Http\Controllers\UpdateTenantSchoolsController;
use App\Http\Controllers\UpdateTimezoneController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Cloud setup links, see Tenant::setupLinks()
 */
Route::middleware(['cloud', 'signed:relative', 'setup_link_open'])
    ->prefix('/setup/{tenant}')
    ->whereNumber('tenant')
    ->group(function () {
        Route::get('/', StartCloudSetupController::class)
            ->name('setup');
        Route::get('/plugin', DownloadPowerSchoolPluginController::class)
            ->name('setup.plugin');
    });

/**
 * District lookup for the root domain or an unknown host, see Installed
 */
Route::middleware('cloud')->group(function () {
    Route::get('/district', [DistrictLookupController::class, 'create'])
        ->name('district.create');
    Route::post('/district', [DistrictLookupController::class, 'store'])
        ->middleware('throttle:district-lookup')
        ->name('district.store');
});

/**
 * Installation wizard, for self-hosted or a cloud setup session
 */
Route::middleware(['self_hosted_or_setup'])
    ->group(function () {
        Route::middleware('uninstalled')
            ->group(function () {
                Route::get('/install', [InstallationController::class, 'index']);
                Route::post('/install', [InstallationController::class, 'store'])
                    ->name('install');
                Route::get('/install/plugin', DownloadPowerSchoolPluginController::class)
                    ->name('install.plugin');
            });

        Route::middleware(['tenant', 'installed', 'no_admin'])
            ->group(function () {
                Route::get('/install/user', [InstallFirstUserController::class, 'index'])
                    ->name('install.user');
                Route::post('/install/user', [InstallFirstUserController::class, 'store']);
                Route::post('/search/sis/user', SisUserController::class);
            });
    });

Route::middleware('tenant')->group(function () {
    Route::post('/demo/login/{userType}', DemoLoginController::class)
        ->middleware('guest')
        ->name('demo.login');

    // PowerSchool auth
    Route::middleware(['sis_configured'])
        ->prefix('/auth/powerschool')
        ->group(function () {
            Route::get('/openid', [PowerSchoolOpenIdLoginController::class, 'authenticate']);
            Route::get('/openid/verify', [PowerSchoolOpenIdLoginController::class, 'login'])
                ->name('openid.verify');
            Route::get('/oidc/authenticate', [PowerSchoolOidcLoginController::class, 'authenticate']);
            Route::get('/oidc', [PowerSchoolOidcLoginController::class, 'login']);
        });

    // TODO: ClassLink auth
    Route::prefix('/auth/classlink')
        ->group(function () {
            Route::get('/oauth', [ClassLinkOAuthController::class, 'authenticate'])
                ->name('classlink.authenticate');
            Route::get('/redirect', [ClassLinkOAuthController::class, 'login']);
        });

    Route::middleware('auth')->group(function () {
        Route::get('/ping', CheckAuthStatusController::class)
            ->name('auth.status');

        Route::get('/csrf-token', RefreshCsrfTokenController::class)
            ->name('csrf-token');

        Route::get('/timezones', fn () => timezones());
        Route::get('/languages', GetLanguagesController::class);

        Route::get('/select-school', [SchoolSelectionController::class, 'index'])
            ->name('select-school');
        Route::post('/select-school', [SchoolSelectionController::class, 'update']);
        Route::post('/sync/{model}/{id}', SyncModelController::class)
            ->middleware('scoped_permissions')
            ->name('model.sync');
        Route::post('/search/{model}', SearchModelController::class)
            ->middleware('scoped_permissions')
            ->name('model.search');

        Route::middleware(['has_school', 'scoped_permissions', 'within_school_limit'])
            ->group(function () {
                Route::match(['post', 'delete'], '/selection/{model}', ToggleSelectionController::class)
                    ->name('selection.toggle');
                Route::get('/selection/{model}', GetSelectionController::class)
                    ->name('selection.get');

                Route::get('/', fn () => inertia('Dashboard', ['title' => __('Dashboard')]))
                    ->name('home');

                Route::get('/activity', [ActivityController::class, 'index'])
                    ->middleware('can:edit school settings')
                    ->name('activity.index');

                Route::resource('/students', StudentController::class)
                    ->only('index', 'show');

                Route::resource('/sections', SectionController::class)
                    ->only('index', 'show');

                Route::resource('/courses', CourseController::class)
                    ->only('index', 'show');

                Route::resource('/users', UserController::class)
                    ->only('index', 'show');

                Route::middleware(Permission::editPermissions->toMiddleware())
                    ->group(function () {
                        Route::get('/users/{user}/permissions', [UserPermissionController::class, 'index'])
                            ->name('users.permissions.index');
                        Route::put('/users/{user}/permissions', [UserPermissionController::class, 'update'])
                            ->name('users.permissions.update');
                    });
            });

        Route::prefix('/settings')
            ->name('settings.')
            ->group(function () {
                Route::singleton('/personal', PersonalSettingsController::class)
                    ->only('edit', 'update');

                Route::put('/timezone', UpdateTimezoneController::class)
                    ->name('timezone.update');

                Route::put('/current-school', UpdateCurrentSchoolController::class)
                    ->name('current-school.update');

                Route::put('/locale', UpdateLocaleController::class)
                    ->name('locale.update');

                Route::middleware('can:edit tenant settings')->group(function () {
                    Route::singleton('/tenant', TenantSettingsController::class)
                        ->only('edit', 'update');

                    Route::put('/tenant/smtp', UpdateSmtpSettingsController::class)
                        ->name('tenant.smtp');

                    Route::post('/tenant/smtp/test', SendSmtpTestController::class)
                        ->name('tenant.smtp.test');

                    Route::put('/tenant/schools', UpdateTenantSchoolsController::class)
                        ->name('tenant.schools');

                    Route::get('/tenant/plugin', DownloadPowerSchoolPluginController::class)
                        ->name('tenant.plugin');
                });

                Route::middleware(['has_school', 'scoped_permissions', 'can:edit school settings'])->group(function () {
                    Route::singleton('/school', SchoolSettingsController::class)
                        ->only('edit', 'update');

                    Route::post('/school/sync/{item}', SyncSchoolItemController::class)
                        ->name('school.item-sync');
                });
            });
    });
});

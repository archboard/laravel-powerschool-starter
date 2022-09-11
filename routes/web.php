<?php

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

if (app()->environment('local')) {
    Route::get('scratch', fn () => inertia('Scratch'));
}

/**
 * Self-hosted only routes
 */
Route::middleware('self_hosted')->group(function () {
    Route::get('/install', \App\Http\Controllers\ShowInstallationPageController::class);
    Route::post('/install', \App\Http\Controllers\CreateTenantController::class)
        ->name('install');
});

Route::middleware('tenant')->group(function () {
    // PowerSchool auth
    Route::middleware(['sis_configured'])
        ->prefix('/auth/powerschool')
        ->group(function () {
            Route::get('/openid', [\App\Http\Controllers\Auth\PowerSchoolOpenIdLoginController::class, 'authenticate']);
            Route::get('/openid/verify', [\App\Http\Controllers\Auth\PowerSchoolOpenIdLoginController::class, 'login'])
                ->name('openid.verify');
            Route::get('/oidc/authenticate', [\App\Http\Controllers\Auth\PowerSchoolOidcLoginController::class, 'authenticate']);
            Route::get('/oidc', [\App\Http\Controllers\Auth\PowerSchoolOidcLoginController::class, 'login']);
        });

    Route::middleware('auth')->group(function () {
        Route::get('/ping', \App\Http\Controllers\CheckAuthStatusController::class)
            ->name('auth.status');

        Route::get('/csrf-token', \App\Http\Controllers\RefreshCsrfTokenController::class)
            ->name('csrf-token');

        Route::get('/', function () {
            return inertia('Index');
        });

        Route::prefix('/settings')->group(function () {
            Route::get('personal', [\App\Http\Controllers\Settings\PersonalSettingsController::class, 'index']);
            Route::post('personal', [\App\Http\Controllers\Settings\PersonalSettingsController::class, 'update'])
                ->name('settings.personal');

            Route::middleware('can:edit tenant settings')->group(function () {
                Route::get('tenant', [\App\Http\Controllers\Settings\TenantSettingsController::class, 'index']);
                Route::post('tenant', [\App\Http\Controllers\Settings\TenantSettingsController::class, 'update'])
                    ->name('settings.tenant');
            });
        });
    });
});

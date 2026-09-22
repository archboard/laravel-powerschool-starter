<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use GrantHolle\PowerSchool\Auth\Traits\AuthenticatesUsingPowerSchoolWithOidc;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PowerSchoolOidcLoginController extends Controller
{
    use AuthenticatesUsingPowerSchoolWithOidc;

    protected function getPowerSchoolUrl(): string
    {
        return Tenant::current()->sis_config['url'] ?? '';
    }

    protected function getClientId(): string
    {
        return Tenant::current()->sis_config['client_id'] ?? '';
    }

    public function getClientSecret(): string
    {
        return Tenant::current()->sis_config['client_secret'] ?? '';
    }

    protected function getRedirectToRoute(string $userType): string
    {
        return '/';
    }

    /**
     * @param  Collection<int|string, mixed>  $data
     */
    protected function authenticated(Request $request, Authenticatable $user, Collection $data): void
    {
        if ($user instanceof User) {
            // @phpstan-ignore-next-line method_exists.alreadyNarrowedType
            if (method_exists($user, 'syncFromSis')) {
                $user->syncFromSis();
            }
        }
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use GrantHolle\PowerSchool\Auth\Traits\AuthenticatesUsingPowerSchoolWithOpenId;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PowerSchoolOpenIdLoginController extends Controller
{
    use AuthenticatesUsingPowerSchoolWithOpenId;

    protected function getRedirectToRoute(string $userType): string
    {
        return '/';
    }

    /**
     * The user has been authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     * @param \Illuminate\Support\Collection $data
     * @return mixed
     */
    protected function authenticated(Request $request, User $user, Collection $data)
    {
        $adminSchools = $data->get('adminSchools', []);

        if (!empty($adminSchools)) {
            $schools = School::whereIn('school_number', $adminSchools)
                ->pluck('id');
            $user->schools()->syncWithoutDetaching($schools);
        }
    }
}

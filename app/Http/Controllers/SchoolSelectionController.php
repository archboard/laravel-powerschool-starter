<?php

namespace App\Http\Controllers;

use App\Exceptions\SisNotConfiguredException;
use App\Http\Resources\SchoolResource;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolSelectionController extends Controller
{
    public function index(Tenant $tenant)
    {
        $schools = $tenant->schools;
        $title = __('Select school');

        throw_if($schools->isEmpty(), new SisNotConfiguredException('No schools configured'));

        return inertia('SchoolSelection', [
            'schools' => SchoolResource::collection($schools),
            'title' => $title,
            'endpoint' => route('select-school'),
        ])->withViewData(compact('title'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'school_id' => [
                'required',
                Rule::exists('schools', 'id')
                    ->where('tenant_id', $tenant->id),
            ],
        ]);

        $user = $request->user();
        $user->update($data);
        $user->schools()->syncWithoutDetaching($data['school_id']);

        session()->flash('success', __('School selected successfully'));

        return to_route('home');
    }
}

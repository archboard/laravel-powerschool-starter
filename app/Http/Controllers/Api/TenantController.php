<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TenantApiResource;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $tenants = Tenant::query()
            ->orderBy('created_at')
            ->paginate();

        return TenantApiResource::collection($tenants);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): TenantApiResource
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'license' => ['required', 'uuid', 'unique:tenants'],
            'domain' => ['required', 'string', 'unique:tenants'],
            'custom_domain' => ['nullable', 'string', 'unique:tenants'],
            'subscription_started_at' => ['required', 'date'],
            'subscription_expires_at' => ['required', 'date'],
        ]);

        /** @var Tenant $tenant */
        $tenant = Tenant::create(Arr::except($data, 'email'));
        $tenant->makeCurrent();

        return new TenantApiResource($tenant);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id): \Illuminate\Http\Response
    {
        return response()->noContent();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(Request $request, $id): \Illuminate\Http\Response
    {
        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id): \Illuminate\Http\Response
    {
        return response()->noContent();
    }
}

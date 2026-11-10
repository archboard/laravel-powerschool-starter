<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class TenantApiResource extends TenantResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            ...parent::toArray($request),
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'school_limit' => $this->resource->school_limit,
            'subscription_expires_at' => $this->resource->subscription_expires_at,
        ];
    }
}

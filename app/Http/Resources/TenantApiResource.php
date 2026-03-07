<?php

namespace App\Http\Resources;

class TenantApiResource extends TenantResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            ...parent::toArray($request),
            'id' => $this->resource->id,
        ];
    }
}

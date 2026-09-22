<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read User $resource */
class UserResource extends JsonResource
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
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'last_first' => $this->resource->last_first,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'email' => $this->resource->email,
            'timezone' => $this->resource->timezone,
            'school_id' => $this->resource->school_id,
            'user_type' => $this->resource->user_type?->value,
            'user_type_display' => $this->resource->user_type?->label(),
            'schools' => SchoolResource::collection($this->whenLoaded('schools')),
            'school' => new SchoolResource($this->whenLoaded('schoool')),
            'permissions' => $this->whenLoaded('school', function (): array {
                /** @var array<int, array{permission: string, selected: bool}> $schoolPermissions */
                $schoolPermissions = $this->resource->getAttribute('school_permissions') ?? [];

                return collect($schoolPermissions)
                    ->mapWithKeys(fn (array $perm) => [$perm['permission'] => $perm['selected']])
                    ->toArray();
            }, []),
        ];
    }
}

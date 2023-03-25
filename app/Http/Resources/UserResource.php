<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'timezone' => $this->timezone,
            'schools' => SchoolResource::collection($this->whenLoaded('schools')),
            'school' => new SchoolResource($this->whenLoaded('schoool')),
            'permissions' => $this->whenLoaded('school', function () {
                return collect($this->school_permissions)
                    ->mapWithKeys(function ($perm) {
                        return [$perm['permission'] => $perm['selected']];
                    });
            }, []),
        ];
    }
}

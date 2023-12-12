<?php

namespace App\Http\Resources;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read Section $resource */
class SectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'sis_id' => $this->resource->sis_id,
            'expression' => $this->resource->expression,
            'external_expression' => $this->resource->external_expression,
            'course' => new CourseResource($this->whenLoaded('course')),
        ];
    }
}

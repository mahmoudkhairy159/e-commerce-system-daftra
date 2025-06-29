<?php

namespace Modules\Category\Transformers\Admin\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'code' => $this->code,
            "image_url" => $this->image_url,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'position' => $this->position,
            'translations' => $this->whenLoaded('translations', $this->getTranslationsArray()),

        ];
    }
}
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
            'parent_id' => $this->parent_id,
            // 'parent' => $this->parent ? [
            //     'id' => $this->parent->id,
            //     'name' => $this->parent->name,
            //     'slug' => $this->parent->slug,
            //     'description' => $this->parent->description,
            //     'code' => $this->parent->code,
            //     "image_url" => $this->parent->image_url,
            //     'status' => $this->parent->status,
            //     'position' => $this->parent->position,

            // ] : null,
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'translations' => $this->whenLoaded('translations', $this->getTranslationsArray()),

        ];
    }
}
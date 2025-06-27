<?php

namespace Modules\Shipping\Transformers\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ShippingMethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'flat_rate' => $this->flat_rate,
            'per_km_rate' => $this->per_km_rate,
            'max_distance' => $this->max_distance,
            'status' => $this->status,
            'type' => $this->type,
            'translations' => $this->getTranslationsArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

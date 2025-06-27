<?php

namespace Modules\Area\Transformers\Admin\City;

use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'country_name' => $this->whenLoaded('country', function () {
                return $this->country->name;
            }),
            'state_name' => $this->whenLoaded('state', function () {
                return $this->state->name;
            }),
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            "name" => $this->name,
            "status" => $this->status,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'translations'=>$this->getTranslationsArray()
        ];
    }
}
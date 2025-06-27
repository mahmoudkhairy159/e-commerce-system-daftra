<?php

namespace Modules\Area\Transformers\Api\City;

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
            "name" => $this->name,

        ];
    }
}
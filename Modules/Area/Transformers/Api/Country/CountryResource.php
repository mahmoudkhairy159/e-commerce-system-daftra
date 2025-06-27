<?php

namespace Modules\Area\Transformers\Api\Country;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'phone_code' => $this->phone_code,
            "name" => $this->name,
            // "name_en" => $this->translate('en')->name,

        ];
    }
}

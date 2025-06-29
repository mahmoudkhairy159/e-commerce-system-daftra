<?php

namespace Modules\User\Transformers\Api\UserAddress;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->whenLoaded('user', function () {
                return $this->user->name;
            }),

            'zip_code' => $this->zip_code,
            'address' => $this->address,
            'type' => $this->type,
            'is_default' => $this->is_default,
            'phone_code' => $this->phone_code,
            'phone' => $this->phone,
            'title' => $this->title,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
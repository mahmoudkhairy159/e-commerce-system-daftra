<?php

namespace Modules\User\Transformers\Api\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\User\Transformers\Api\UserAddress\UserAddressResource;
use Modules\User\Transformers\Api\UserProfile\UserProfileResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'email' => $this->email,
            'phone' => $this->phone,
            'name' => $this->name,
            "image_url" => $this->image_url,
            'status' => $this->status,
            'active' => $this->active,
            'blocked' => $this->blocked,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'email_verified_at' => $this->email_verified_at,
            'orders_count' => $this->orders_count,
            'profile' => new UserProfileResource($this->whenLoaded('profile')),
            'user_addresses' =>  UserAddressResource::collection($this->whenLoaded('userAddresses')),
            'default_address' => new UserAddressResource($this->whenLoaded('defaultAddress')),

        ];
    }
}
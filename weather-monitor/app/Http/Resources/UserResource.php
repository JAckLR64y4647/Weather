<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
			'roles' => $this->roles->pluck('name'),
            'created_at' => $this->created_at,
            'is_email_verified' => $this->hasVerifiedEmail(),
			'location_views' => $this->locationViews->pluck('location_id'),
			'favourite_locations' => $this->favouriteLocations->pluck('id'),
        ];
    }
}

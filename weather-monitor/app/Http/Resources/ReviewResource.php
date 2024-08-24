<?php

namespace App\Http\Resources;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Review */
class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
			'id' => $this->id,
			'comment' => $this->comment,
			'user' => [
				'id' => $this->user->id,
				'name' => $this->user->name,
			],
			'created_at' => $this->created_at,
		];
    }
}

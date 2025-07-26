<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'status' => $this->status,
            'total_price' => $this->total_price,
        ];

        if ($this->relationLoaded('items')) {
            $data['items'] = OrderItemResource::collection($this->items);
        }

        if ($this->relationLoaded('user')) {
            $data['user'] = new UserResource($this->user);
        }

        return $data;
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class DailyFoodPriceResource extends JsonResource
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
            'date' => $this->date,
            'food_id' => $this->food_id,
            'food_name' => $this->food_name,
            'price' => $this->price,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s '),
        ];
    }
}

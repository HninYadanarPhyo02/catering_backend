<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'fb_id' => $this->fb_id,
            'emp_id' => $this-> emp_id,
            'text' => $this->text,
            'rating' => $this-> rating,
            'updated_at' => Carbon::parse($this->updated_at)->Format('Y-m-d H:i:s '),
        ];
    }
}

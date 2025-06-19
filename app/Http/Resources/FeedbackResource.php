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
            'emp_name' => $this->employee->name ?? null,
            'emp_email' => $this->employee->email ?? null,
            'text' => $this->text,
            'rating' => $this-> rating,
            'updated_at' => Carbon::parse($this->updated_at)->Format('Y-m-d H:i:s '),
        ];
    }
}

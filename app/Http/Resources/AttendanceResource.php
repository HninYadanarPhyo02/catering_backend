<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'emp_id' => $this->emp_id,
            'date' => $this->date,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s '),
        ];
    }
}

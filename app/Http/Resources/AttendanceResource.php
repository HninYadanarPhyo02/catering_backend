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
        // return [
        //     'id' => $this->id,
        //     'emp_id' => $this->emp_id,
        //     'status' => $this->status,
        //     'check_out' => $this->check_out,
        //     'date' => $this->date,
        //     'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s '),
        // ];
        return [
        'emp_id'     => $this->emp_id,
        'emp_name'   => optional($this->employee)->name,
        'date'       => $this->date,
        'food_name'  => optional($this->foodmonthprice)->food_name,
        'price'      => optional($this->foodmonthprice)->price,
        'total'      => optional($this->foodmonthprice)->price,
        'status'     => $this->status,
        'check_out'  => $this->check_out,
    ];
    }
}

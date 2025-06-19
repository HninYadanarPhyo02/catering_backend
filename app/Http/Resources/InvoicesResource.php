<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoicesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'invoice_id' => $this->invoice_id,
            'emp_id' => $this->emp_id,
            'month' => $this->month,
            'year' => $this->year,
            'total_amount' => $this->total_amount,
            'details' => InvoiceDetailResource::collection($this->whenLoaded('details')),
            // optionally add timestamps or employee info if needed
        ];
    }
}

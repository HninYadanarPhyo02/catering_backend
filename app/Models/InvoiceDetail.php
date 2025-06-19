<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'date',
        'food_name',
        'price',
        'status',
        'check_out',
    ];

    // Relationships
    public function invoice()
{
    return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
}
protected static function booted()
{
    static::deleting(function ($detail) {
        if ($detail->invoice) {
            $detail->invoice->decrement('total_amount', $detail->price);
        }
    });
}

}

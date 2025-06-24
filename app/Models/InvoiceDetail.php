<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];

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
                // This works only for hard deletes (forceDelete)
                $detail->invoice->decrement('total_amount', $detail->price);
            }
        });

        static::updated(function ($detail) {
            // If 'deleted_at' was just updated, recalculate invoice total
            if ($detail->isDirty('deleted_at')) {
                $invoice = $detail->invoice;
                if ($invoice) {
                    $total = $invoice->details()->whereNull('deleted_at')->sum('price');
                    $invoice->update(['total_amount' => $total]);
                }
            }
        });
    }
}

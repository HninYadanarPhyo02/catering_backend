<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    protected $primaryKey = 'invoice_id';

    public $incrementing = false; // Since UUIDs are not integers
    protected $keyType = 'string';


    protected $fillable = [
        'invoice_id',
        'emp_id',
        'month',
        'year',
        'total_amount',
    ];

    // Relationships
    public function details()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id', 'invoice_id');
    }

    // If you want to use invoice_number as primary key (optional):
    // protected $primaryKey = 'invoice_number';

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically generate UUID when creating a new Invoice
        // static::creating(function ($model) {
        //     if (empty($model->invoice_number)) {
        //         $model->invoice_number = (string) Str::uuid();
        //     }
        // });
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }
    public static function recalculateInvoiceTotal($invoice_id)
    {
        $total = InvoiceDetail::where('invoice_id', $invoice_id)->sum('price');
        Invoice::where('invoice_id', $invoice_id)->update(['total_amount' => $total]);
    }
}

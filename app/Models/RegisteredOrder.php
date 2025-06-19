<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegisteredOrder extends Model
{
    use SoftDeletes;

    protected $table = 'registered_order';
    protected $dates = ['deleted_at'];

    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'emp_id',
        'date',
    ];
    // ✅ METHOD TO REGISTER A NEW ORDER
    public static function register($empId, $date): self
    {
        $existing = self::withTrashed()
            ->where('emp_id', $empId)
            ->whereDate('date', $date)
            ->first();

        if ($existing && !$existing->trashed()) {
            throw new \Exception("Employee already registered on this date.");
        }

        if ($existing && $existing->trashed()) {
            $existing->restore(); // Restore the soft-deleted record
            return $existing;
        }

        return self::create([
            'id' => (string) Str::uuid(),
            'emp_id' => $empId,
            'date' => $date,
        ]);
    }

    // app/Models/RegisteredOrder.php
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }


    // public function foodMonthPrice()
    // {
    //     return $this->belongsTo(FoodMonthPrice::class, 'food_price_id'); // Make sure this field exists
    // }

    public function foodMonthPricesByDate()
    {
        return $this->hasMany(FoodMonthPrice::class, 'date', 'date');
    }
    // public function attendance()
    // {
    //     return $this->hasOne(Attendance::class, 'emp_id', 'emp_id')
    //                 ->whereColumn('date', 'registeredorder.date');
    // }
    public function announcement()
    {
        return $this->hasOne(Announcement::class, 'date', 'date');
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class, 'emp_id', 'emp_id')
            ->whereColumn('date', 'registered_order.date'); // Match on both emp_id and date
    }
}

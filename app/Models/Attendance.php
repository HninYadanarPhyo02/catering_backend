<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'attendance';
    public $timestamps = true;
    protected $casts = [
        'check_out' => 'boolean',
    ];


    protected $fillable = [
        'emp_id',
        'date',
        'status',
        'check_out'
    ];
    public static function findByEmpId($emp_id)
    {
        return self::where('emp_id', $emp_id)->first();
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }
    public function foodmonthprice()
    {
        return $this->belongsTo(FoodMonthPrice::class, 'food_id', 'food_id');
    }
    // In Attendance model
    public function foodmonthpriceByDate()
    {
        return $this->hasOne(FoodMonthPrice::class, 'date', 'date');
    }
    public function registeredOrder()
    {
        return $this->belongsTo(RegisteredOrder::class, 'emp_id', 'emp_id')
            ->whereColumn('date', 'registered_order.date'); // Match on both emp_id and date
    }
}

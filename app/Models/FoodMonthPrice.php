<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodMonthPrice extends Model
{
    use SoftDeletes;
    protected $table = 'foodmonthprice';

    protected $fillable = [
        // 'employee_id',
        'date',
        'food_name',
        'food_id',
        'price'
    ];


    public static function findByName($food_name)
    {
        return self::whereRaw('LOWER(food_name) = ?', [strtolower($food_name)]);
    }

    public function foodMenu()
    {
        return $this->belongsTo(FoodMenu::class, 'food_id','food_id');
    }
    // public function registerOrders()
    // {
    //     return $this->hasMany(RegisteredOrder::class, 'food_price_id'); // Assuming foreign key
    // }
    public function registeredOrdersByDate()
    {
        return $this->hasMany(RegisteredOrder::class, 'date', 'date');
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'food_id', 'food_id');
    }
}

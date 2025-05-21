<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodMonthPrice extends Model
{
    protected $table='foodmonthprice';

    protected $fillable=[
        'date',
        'food_name',
        'price'
    ];
    

    public static function findByName($food_name){
    return self::whereRaw('LOWER(food_name) = ?', [strtolower($food_name)]);
}

}


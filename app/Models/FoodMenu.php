<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class FoodMenu extends Model
{

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'foodmenu';
    public $timestamps = true;


    protected $fillable = [
        'food_id',
        'name'
    ];

    // public static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         if (empty($model->{$model->getKeyName()})) {
    //             $model->{$model->getKeyName()} = (string) Str::uuid();
    //         }
    //     });
    // }

    //  protected static function booted()
    // {
    //     static::creating(function ($model) {
    //         if (empty($model->food_id)) {
    //             $model->food_id = 'food_' . Str::random(6); // or use uuid: Str::uuid()
    //         }
    //     });
    // }
    public static function findByName($name)
    {
        return self::whereRaw('LOWER(name) = ?', [strtolower($name)]);
    }
    public static function findByDate($date)
    {
        return self::where('date',$date);
    }
//    public function foodMonthPrices(): HasMany
//     {
//     return $this->hasMany(FoodMonthPrice::class, 'food_id', 'id');
//     }
    public function prices()
    {
        return $this->hasMany(FoodMonthPrice::class,'food_id');
    }
//     public function static(){
//         $name = FoodMenu::with('prices')->get();
//     }
    


// public function index() {
//     $results = DB::table('foodmenu')
//         ->leftJoin('foodmonthprice', 'foodmenu.id', '=', 'foodmonthprice.food_id')
//         ->select(
//             'foodmenu.id as food_id',
//             'foodmenu.name as food_name',
//             'foodmonthprice.date',
//             'foodmonthprice.price'
//         )
//         ->get();

//     return response()->json($results);
// }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class FoodMenu extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'foodmenu';
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'food_id',
        'name'
    ];

    
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

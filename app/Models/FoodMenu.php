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


    protected $fillable=[
        'name'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public static function findByName($name){
    return self::whereRaw('LOWER(name) = ?', [strtolower($name)]);
}

}

<?php

namespace App\Models;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $primaryKey = 'emp_id';
    public $incrementing = false;  // important for non-numeric keys
    protected $keyType = 'string';  // since 'emp_0002' is a string

    protected $table = 'employee';

    protected $fillable = [
        'id',
        'emp_id',
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // public function foodMonthPrices()
    // {
    //     return $this->hasMany(FoodMonthPrice::class, 'employee_id', 'emp_id');
    // }
    public function isAdmin()
    {
        return $this->role === 'admin';
    }


    public function getRouteKeyName()
    {
        return 'emp_id';
    }
    public function registerOrders()
    {
        return $this->hasMany(RegisteredOrder::class, 'emp_id');
    }
    // Define one-to-many relationship: one employee has many feedbacks
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'emp_id', 'emp_id');
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'emp_id', 'emp_id');
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'emp_id', 'emp_id');
    }
}

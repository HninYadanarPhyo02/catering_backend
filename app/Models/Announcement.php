<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'announcement';
    protected $fillable = [
        'date',
        'title',
        'text',
    ];
    public function registeredOrders()
    {
        return $this->hasMany(RegisteredOrder::class, 'date', 'date');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
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

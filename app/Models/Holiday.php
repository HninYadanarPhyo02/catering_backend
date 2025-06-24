<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table='holidays';
    protected $fillable = ['h_id', 'name', 'date', 'description'];
}

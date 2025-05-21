<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
    public $timestamps = true;

    protected $fillable = [
        'emp_id',
        'date'
    ];
    public static function findByEmpId($emp_id)
    {
        return self::where('emp_id', $emp_id)->first();
    }
}

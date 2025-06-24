<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Feedback extends Model
{
    use HasApiTokens, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table= 'feedback';
    protected $primaryKey = 'fb_id';  // Tell Laravel the primary key is 'fb_id'

    protected $fillable=[
        'emp_id',
        'text',
        'rating'
    ];
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    // public function employee(){
    //     return $this->belongsTo(Employee::class,'emp_id');
    // }
 public static function findByEmpId($emp_id)
{
    return self::where('emp_id', $emp_id)->get(); // Returns a collection
}

    // Define inverse relationship: feedback belongs to an employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }
}

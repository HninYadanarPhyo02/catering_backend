<?php

use App\Http\Controllers\Api\Auth\AnnouncementController;
use App\Http\Controllers\Api\Auth\AttendanceController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\FoodController;
use App\Http\Controllers\Api\Auth\FoodMonthPriceController;
use App\Http\Controllers\Api\Auth\FeedbackController;
use App\Http\Controllers\EmployeeController;
use App\Models\FoodMonthPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login',[AuthController::class,'login']);
// Route::post('/employees/import-base64', [EmployeeController::class, 'importBase64']);
Route::post('/employees/import', [EmployeeController::class, 'importAndInsert']);
#CRUD of food
Route::post('/food/create', [FoodController::class, 'store']);
Route::put('/food/update/{name}', [FoodController::class, 'update']);
Route::delete('/food/destroy/{name}', [FoodController::class, 'destroy']);
Route::get('/food/show/{name}', [FoodController::class, 'show']);
Route::get('/food/list',[FoodController::class,'list']);

#CRUD of foodmonthprice
Route::post('/foodmonth/create',[FoodMonthPriceController::class,'create']);
Route::get('/foodmonth/show/{food_name}',[FoodMonthPriceController::class,'show']);
Route::get('/foodmonth/list',[FoodMonthPriceController::class,'list']);
Route::put('/foodmonth/update/{food_name}',[FoodMonthPriceController::class,'update']);
Route::delete('/foodmonth/destroy/{food_name}',[FoodMonthPriceController::class,'destroy']);

#CRUD of Feedback but can't update
Route::post('/feedback/create',[FeedbackController::class,'store']);
Route::get('/feedback/show/{emp_id}',[FeedbackController::class,'show']);
Route::get('/feedback/list',[FeedbackController::class,'list']);
Route::delete('/feedback/destroy/{fb_id}',[FeedbackController::class,'destroy']);

#CRUD of Attendance but can't update
Route::post('/attendance/store',[AttendanceController::class,'store']);
Route::get('/attendance/show/{emp_id}',[AttendanceController::class,'show']);
Route::get('/attendance/list',[AttendanceController::class,'list']);
Route::delete('/attendance/destroy/{id}',[AttendanceController::class,'destroy']);

#CRUD of Announcement 
Route::post('/announcement/create',[AnnouncementController::class,'create']);
Route::get('/announcement/show/{date}',[AnnouncementController::class,'show']);
Route::get('/announcement/list',[AnnouncementController::class,'list']);
Route::put('/announcement/update/{id}',[AnnouncementController::class,'update']);
Route::delete('/announcement/destroy/{id}',[AnnouncementController::class,'destroy']);



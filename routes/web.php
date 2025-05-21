<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Symfony\Component\HttpKernel\Event\ViewEvent;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', function () {
    return view('index');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/reservation', [ReservationController::class, 'index'])->name('reservation');
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/employee', [EmployeeController::class, 'index'])->name('employee');
Route::get('/setting', [SettingController::class, 'index'])->name('setting');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Route::get('/dashboard1', [DashboardController::class, 'index'])->name('dashboard');
// Route::resource('reservations', ReservationController::class);
// Route::resource('menus', MenuController::class);
// Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');


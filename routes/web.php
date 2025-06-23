<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\FoodMonthPrice;
use App\Mail\MonthlyReportMail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use App\Http\Controllers\Api\Auth\HolidayController;
use App\Http\Controllers\Api\Auth\InvoiceController;
use App\Http\Controllers\Api\Auth\FeedbackController;

// Default login page
Route::get('/', function () {
    return view('auth.login');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Auth routes
require __DIR__ . '/auth.php';

// Register
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');


// Login
Route::post('adminLogin', [AuthController::class, 'adminLogin'])->name('loginAuth');

// Logout
Route::post('/', [AuthController::class, 'logout'])->name('logout');

//CRUD of menu
Route::resource('menus', MenuController::class);
// Route::post('/menus/index',[MenuController::class,'index'])->name('index');

//CRUD of customer
Route::resource('customers', CustomersController::class);
Route::put('/customers/{emp_id}', [CustomersController::class, 'update'])->name('customers.update');
Route::post('/customers/import', [CustomersController::class, 'import'])->name('customers.import');


//ReportController
// Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

//CRUD of dailyfoodprice
Route::resource('orders', OrderController::class);
Route::delete('/orders/delete-by-date/{date}', [OrderController::class, 'destroyByDate'])->name('orders.destroyByDate');
// Route::delete('/orders/delete-by-date/{date}', [OrderController::class, 'destroyByDate'])->name('orders.destroyByDate');


Route::resource('invoices', InvoicesController::class);
Route::get('/invoices', [InvoicesController::class, 'index'])->name('invoices.index');
Route::post('/invoices/generate', [InvoicesController::class, 'generateInvoice'])->name('invoices.generate');
Route::get('/admin/invoices/{invoice_id}', [InvoicesController::class, 'show'])->name('invoices.show');



Route::get('/payments', [PaymentController::class, 'index'])->name('payment');

// Payment routes
// Route::get('/payments/create/{invoiceId}', [PaymentController::class, 'create'])->name('payments.create');
Route::get('/payments/create/{invoice}', [PaymentController::class, 'create'])->name('payments.create');
Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store');
Route::get('/orders/{order}/payment', [PaymentController::class, 'showPaymentForm'])->name('payments.form');
Route::post('/orders/{order}/payment', [PaymentController::class, 'processPayment'])->name('payments.process');

//Feedback
Route::get('feedbacks', [FeedbacksController::class, 'index'])->name('feedback');
Route::get('/feedback/details/{emp_id}', [FeedbacksController::class, 'details'])->name('feedback.detail');



//Holidays
Route::post('/holidays/upload', [HolidaysController::class, 'uploadExcel'])->name('holidays.upload');
Route::post('/holidays', [HolidaysController::class, 'store'])->name('holidays.store');
Route::resource('holidays', HolidaysController::class)->except(['show']);
Route::get('/holidays/{h_id}/edit', [HolidaysController::class, 'edit'])->name('holidays.edit');
Route::put('/holidays/{h_id}', [HolidaysController::class, 'update'])->name('holidays.update');
Route::delete('/holidays/{h_id}', [HolidaysController::class, 'destroy'])->name('holidays.destroy');

//Announcement
Route::resource('announcements', AnnouncementController::class);
Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcement');
Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');

Route::put('/announcements/{id}', [AnnouncementController::class, 'update'])->name('announcements.update');
Route::get('/announcements/{id}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');



//Attendance
Route::resource('/attendance', AttendanceController::class);
// Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('/attendance/details/{emp_id}', [AttendanceController::class, 'details'])->name('attendance.details');

//Setting
Route::get('/settings', [SettingsController::class, 'index']);
Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');


//RegisteredOrder
Route::resource('registeredorder', RegisteredOrderController::class);
Route::get('registeredorder', [RegisteredOrderController::class, 'index'])->name('registeredorder');
Route::get('/registered-orders/{id}', [RegisteredOrderController::class, 'show'])->name('registered-orders.show');
Route::get('/registered-orders/employee/{emp_id}', [RegisteredOrderController::class, 'showByEmployee'])->name('registered-orders.details');




//Dashboard
// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// Route::get('/dashboard', [DashboardController::class, 'index']);

//Settings
Route::resource('settings', SettingsController::class);



//CRUD of Invoice
// Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
// Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');


// Route::put('customers/update', [CustomersController::class, 'update'])->name('customers.update');
// Route::put('/customers',[CustomersController::class,'update'])->name('customers.edit');
// Route::get('/profile', [ProfileController::class, 'show'])->name('profile')->middleware('auth');
// Route::get('/profile',[ProfileController::class, 'show'])->name('profile');
Route::get('/profile', [adminProfileController::class, 'index'])->name('profile');
Route::patch('/profile', [adminProfileController::class, 'update'])->name('profile.update');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Profile
    // Route::get('/profile', [adminProfileController::class, 'edit'])->name('profile.edit');
    // Route::get('/profile', [adminProfileController::class, 'shows'])->name('profile.show');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::get('adminPage', [CategoryController::class, 'index'])->name('categoryList');
    // Route::get('/dashboard', [DashboardController::class, 'index'])
    // ->middleware(['auth', 'verified'])
    // ->name('categoryList');

    // Others
    Route::get('/orders', [OrderController::class, 'index'])->name('order');
    Route::get('/menu', [MenuController::class, 'index'])->name('menu');
    Route::get('/holidays', [HolidaysController::class, 'index'])->name('holidays');
    Route::get('/customersmanagement', [CustomersController::class, 'index'])->name('cusmang');
    Route::get('/invoices&payments', [InvoicesController::class, 'index'])->name('invoices');
    Route::get('/reports&analytics', [ReportsController::class, 'index'])->name('reports');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    // Example route (adjust controller and method names as needed)
    Route::post('/invoices/{invoice_id}/send-mail', [InvoicesController::class, 'sendInvoiceMail'])->name('invoices.send-mail');
    //For all employee
    Route::post('/invoices/send-all', [InvoicesController::class, 'sendAllMonthlyInvoices'])->name('invoices.send-all');

});

// Forgot Password
Route::get('forgotPasswordPage', [AuthController::class, 'forgotPasswordPage'])->name('forgotPasswordPage');
Route::post('forgotPassword', [AuthController::class, 'forgotPassword'])->name('forgotPassword');
Route::get('resetPasswordPage/{token}', [AuthController::class, 'resetPasswordPage'])->name('resetPasswordPage');
Route::post('resetPassword', [AuthController::class, 'resetPassword'])->name('resetPassword');

// Forgot Password Blade Page
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

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
Route::get('/', fn() => view('auth.login'));

// Register
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Login
Route::post('adminLogin', [AuthController::class, 'adminLogin'])->name('loginAuth');

// Logout
Route::post('/', [AuthController::class, 'logout'])->name('logout');


// Forgot Password
Route::get('forgotPasswordPage', [AuthController::class, 'forgotPasswordPage'])->name('forgotPasswordPage');
Route::post('forgotPassword', [AuthController::class, 'forgotPassword'])->name('forgotPassword');
Route::get('resetPasswordPage/{token}', [AuthController::class, 'resetPasswordPage'])->name('resetPasswordPage');
Route::post('resetPassword', [AuthController::class, 'resetPassword'])->name('resetPassword');

// Blade forgot password page
Route::get('/forgot-password', fn() => view('auth.forgot-password'))->middleware('guest')->name('password.request');

// Auth routes
require __DIR__ . '/auth.php';


// ========== PROTECTED ROUTES ==========

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Menu
    Route::resource('menus', MenuController::class);

    // Customers
    Route::resource('customers', CustomersController::class);
    Route::put('/customers/{emp_id}', [CustomersController::class, 'update'])->name('customers.update');
    Route::post('/customers/import', [CustomersController::class, 'import'])->name('customers.import');

    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

    // Orders
    Route::resource('orders', OrderController::class);
    Route::get('orders', [OrderController::class,'index'])->name('order');
    Route::delete('/orders/delete-by-date/{date}', [OrderController::class, 'destroyByDate'])->name('orders.destroyByDate');

    // Invoices
    Route::resource('invoices', InvoicesController::class);
    Route::get('/admin/invoices/{invoice_id}', [InvoicesController::class, 'show'])->name('invoices.show');
    Route::post('/invoices/generate', [InvoicesController::class, 'generateInvoice'])->name('invoices.generate');
    Route::post('/invoices/{invoice_id}/send-mail', [InvoicesController::class, 'sendInvoiceMail'])->name('invoices.send-mail');
    Route::post('/invoices/send-all', [InvoicesController::class, 'sendAllMonthlyInvoices'])->name('invoices.send-all');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payment');
    Route::get('/payments/create/{invoice}', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/orders/{order}/payment', [PaymentController::class, 'showPaymentForm'])->name('payments.form');
    Route::post('/orders/{order}/payment', [PaymentController::class, 'processPayment'])->name('payments.process');

    // Feedbacks
    Route::get('feedbacks', [FeedbacksController::class, 'index'])->name('feedback');
    Route::get('/feedback/details/{emp_id}', [FeedbacksController::class, 'details'])->name('feedback.detail');

    // Holidays
    Route::post('/holidays/upload', [HolidaysController::class, 'uploadExcel'])->name('holidays.upload');
    Route::post('/holidays', [HolidaysController::class, 'store'])->name('holidays.store');
    Route::resource('holidays', HolidaysController::class)->except(['show']);
    Route::get('/holidays/{h_id}/edit', [HolidaysController::class, 'edit'])->name('holidays.edit');
    Route::put('/holidays/{h_id}', [HolidaysController::class, 'update'])->name('holidays.update');
    Route::delete('/holidays/{h_id}', [HolidaysController::class, 'destroy'])->name('holidays.destroy');

    // Announcements
    Route::resource('announcements', AnnouncementController::class);
    Route::get('announcements', [AnnouncementController::class,'index'])->name('announcement');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::get('/announcements/{id}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');

    // Attendance
    Route::resource('attendance', AttendanceController::class);
    Route::get('/attendance/details/{emp_id}', [AttendanceController::class, 'details'])->name('attendance.details');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::resource('settings', SettingsController::class);

    // Registered Orders
    Route::resource('registeredorder', RegisteredOrderController::class);
    Route::get('registeredorder', [RegisteredOrderController::class,'index'])->name('registeredorder');
    Route::get('/registered-orders/{id}', [RegisteredOrderController::class, 'show'])->name('registered-orders.show');
    Route::get('/registered-orders/employee/{emp_id}', [RegisteredOrderController::class, 'showByEmployee'])->name('registered-orders.details');

    // Profile
    Route::get('/profile', [adminProfileController::class, 'index'])->name('profile');
    Route::patch('/profile', [adminProfileController::class, 'update'])->name('profile.update');

    // Admin category page
    Route::get('adminPage', [CategoryController::class, 'index'])->name('categoryList');

    // Extra UI routes (optional, depends on view file usage)
    Route::get('/menu', [MenuController::class, 'index'])->name('menu');
    Route::get('/holidays', [HolidaysController::class, 'index'])->name('holidays');
    Route::get('/customersmanagement', [CustomersController::class, 'index'])->name('cusmang');
    Route::get('/invoices&payments', [InvoicesController::class, 'index'])->name('invoices');
    Route::get('/reports&analytics', [ReportsController::class, 'index'])->name('reports');

});

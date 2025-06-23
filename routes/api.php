<?php

use App\Models\Holiday;
use App\Models\Invoice;
use App\Models\FoodMenu;
use Illuminate\Http\Request;
use App\Models\InvoiceDetail;
use App\Mail\MonthlyReportMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\FoodController;
use App\Http\Controllers\InvoiceDetailController;
use App\Http\Controllers\Api\Auth\HolidayController;
use App\Http\Controllers\Api\Auth\InvoiceController;
use App\Http\Controllers\Api\Auth\FeedbackController;
use App\Http\Controllers\Api\Auth\DashboardController;
use App\Http\Controllers\Api\Auth\AttendanceController;
use App\Http\Controllers\Api\Auth\AnnouncementController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\Api\Auth\FoodMonthPriceController;
use App\Http\Controllers\Api\Auth\RegisteredOrderController;
use App\Http\Controllers\AttendanceController as ControllersAttendanceController;
use App\Http\Controllers\Api\Auth\RegisteredOrderController as AuthRegisteredOrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);

// // Route::post('/employees/import-base64', [EmployeeController::class, 'importBase64']);
// Route::post('/employees/import', [EmployeeController::class, 'importBase64']);

// Route::post('/holidays/import', [HolidayController::class, 'importBase64']);


// #CRUD of employee
// Route::get('/employees/list', [EmployeeController::class, 'list']);
// Route::get('/employees/show/{name}', [EmployeeController::class, 'show']);
// Route::put('/employees/update/{emp_id}', [EmployeeController::class, 'update']);
// Route::delete('/employees/destroy/{emp_id}', [EmployeeController::class, 'destroy']);

// #CRUD of food
// Route::post('/food/create', [FoodController::class, 'store']);
// Route::put('/food/update/{name}', [FoodController::class, 'update']);
// Route::delete('/food/destroy/{name}', [FoodController::class, 'destroy']);
// Route::get('/food/show/{name}', [FoodController::class, 'show']);
// Route::get('/food/list', [FoodController::class, 'list']);

// #CRUD of foodmonthprice
// Route::post('/foodmonth/create', [FoodMonthPriceController::class, 'create']);
// Route::get('/foodmonth/show/{date}', [FoodMonthPriceController::class, 'show']);
// Route::get('/foodmonth/list', [FoodMonthPriceController::class, 'list']);
// Route::put('/foodmonth/update/{date}', [FoodMonthPriceController::class, 'update']);
// Route::delete('/foodmonth/destroy/{date}', [FoodMonthPriceController::class, 'destroy']);

// #CRUD of Feedback but can't update
// Route::post('/feedback/create', [FeedbackController::class, 'store']);
// Route::get('/feedback/show/{emp_id}', [FeedbackController::class, 'show']);
// Route::get('/feedback/list', [FeedbackController::class, 'list']);
// Route::delete('/feedback/destroy/{fb_id}', [FeedbackController::class, 'destroy']);

// #CRUD of Attendance but can't update
// Route::post('/attendance/store', [AttendanceController::class, 'store']);
// Route::get('/attendance/show/{emp_id}', [AttendanceController::class, 'show']);
// Route::get('/attendance/list', [AttendanceController::class, 'list']);
// Route::delete('/attendance/destroy/{id}', [AttendanceController::class, 'destroy']);

// //CRUD of registeredorder
// Route::post('/registered-orders/store', [RegisteredOrderController::class, 'store']);
// Route::get('/registered-orders/lists', [RegisteredOrderController::class, 'lists']);
// Route::get('/registered-orders/{emp_id}', [RegisteredOrderController::class, 'showFoodPricesByDate']);


// #CRUD of Announcement 
// Route::post('/announcement/create', [AnnouncementController::class, 'create']);
// Route::get('/announcement/show/{date}', [AnnouncementController::class, 'show']);
// Route::get('/announcement/list', [AnnouncementController::class, 'list']);
// Route::put('/announcement/update/{id}', [AnnouncementController::class, 'update']);
// Route::delete('/announcement/destroy/{id}', [AnnouncementController::class, 'destroy']);

// #CRUD of Holiday
// Route::post('/holiday/create', [HolidayController::class, 'store']);
// Route::get('/holiday/show/{date}', [HolidayController::class, 'show']);
// Route::get('/holiday/list', [HolidayController::class, 'list']);
// Route::put('/holiday/update/{date}', [HolidayController::class, 'update']);
// Route::delete('/holiday/destroy/{date}', [HolidayController::class, 'destroy']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Route::post('/employees/import-base64', [EmployeeController::class, 'importBase64']);
    Route::post('/employees/import', [EmployeeController::class, 'importBase64']);


    //Invoice 
    // Route::apiResource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice_id}', [InvoiceController::class, 'show']);
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::post('/invoices/generate', [InvoiceController::class, 'generateInvoice']);
    //Holiday
    Route::post('/holidays/import', [HolidayController::class, 'importBase64']);

    #CRUD of employee
    Route::get('/employees/list', [EmployeeController::class, 'list']);
    Route::get('/employees/show/{emp_id}', [EmployeeController::class, 'showInfo']);
    Route::get('/employees/showPsw/{emp_id}', [EmployeeController::class, 'showPsw']);
    Route::put('/employees/{emp_id}', [EmployeeController::class, 'updateInfo']);
    Route::put('/employeesPsw/{emp_id}', [EmployeeController::class, 'updatePsw']);
    Route::put('/admins/{admin_id}', [EmployeeController::class, 'updateforAdmin']);
    Route::delete('/employees/destroy/{emp_id}', [EmployeeController::class, 'destroy']);
    Route::get('/employees/attendance/{emp_id}', [EmployeeController::class, 'getEmployeeAttendance']);

    #CRUD of food
    Route::post('/food/create', [FoodController::class, 'store']);
    Route::put('/food/update/{name}', [FoodController::class, 'update']);
    Route::delete('/food/destroy/{name}', [FoodController::class, 'destroy']);
    Route::get('/food/show/{name}', [FoodController::class, 'show']);
    Route::get('/food/list', [FoodController::class, 'list']);

    #CRUD of foodmonthprice
    Route::post('/foodmonth/create', [FoodMonthPriceController::class, 'create']);
    Route::get('/foodmonth/show/{date}', [FoodMonthPriceController::class, 'show']);
    Route::get('/foodmonth/list', [FoodMonthPriceController::class, 'list']);
    Route::put('/foodmonth/update/{date}', [FoodMonthPriceController::class, 'update']);
    Route::delete('/foodmonth/destroy/{date}', [FoodMonthPriceController::class, 'destroy']);

    #CRUD of Feedback but can't update
    Route::post('/feedback/create', [FeedbackController::class, 'store']);
    Route::get('/feedback/show/{emp_id}', [FeedbackController::class, 'show']);
    Route::get('/feedback/list', [FeedbackController::class, 'list']);
    Route::delete('/feedback/destroy/{fb_id}', [FeedbackController::class, 'destroy']);

    //Invoice
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::post('/invoices/generate', [InvoiceController::class, 'generateMonthlyInvoices']);
    Route::post('/invoices/generateforEmp', [InvoiceController::class, 'generateInvoiceForLoggedInEmployee']);
    Route::delete('/invoice-details/{invoce_id}', [InvoiceDetailController::class, 'destroy']);


    #CRUD of Attendance but can't update
    Route::post('/attendance', [AttendanceController::class, 'store']);
    Route::get('/attendance/show/{emp_id}', [AttendanceController::class, 'show']);
    Route::get('/attendance/list', [AttendanceController::class, 'list']);
    Route::get('/attendance/list-admin', [AttendanceController::class, 'invoice']); //for admin
    Route::delete('/attendance/destroy/{id}', [AttendanceController::class, 'destroy']);
    Route::get('/get-orders', [AttendanceController::class, 'getOrdersByToken']);

    #CRUD of Announcement 
    Route::post('/announcement/create', [AnnouncementController::class, 'create']);
    Route::get('/announcement/show/{date}', [AnnouncementController::class, 'show']);
    Route::get('/announcement/list', [AnnouncementController::class, 'list']);
    Route::put('/announcement/update/{id}', [AnnouncementController::class, 'update']);
    Route::delete('/announcement/destroy/{id}', [AnnouncementController::class, 'destroy']);

    #CRUD of Holiday
    Route::post('/holiday/create', [HolidayController::class, 'store']);
    Route::post('/holiday/import-base64', [HolidayController::class, 'importBase64']);
    Route::get('/holiday/show/{date}', [HolidayController::class, 'show']);
    Route::get('/holiday/list', [HolidayController::class, 'list']);
    Route::put('/holiday/update/{date}', [HolidayController::class, 'update']);
    Route::delete('/holiday/destroy/{date}', [HolidayController::class, 'destroy']);

    //CRUD of registeredorder
    Route::post('/registered-orders/store', [RegisteredOrderController::class, 'store']);
    Route::get('/registered-orders/lists', [RegisteredOrderController::class, 'lists']);
    Route::get('/registered-orders/{emp_id}', [RegisteredOrderController::class, 'showFoodPricesByDate']);

    //AnnouncementController
    Route::resource('announcements', AnnouncementController::class);

    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    // Route::get('/dashboard/MonthlyOrderCounts',[DashboardController::class,'getMonthlyAttendedMenus']);
    // For API
    Route::get('/dashboard/MonthlyOrderCounts', [DashboardController::class, 'getMonthlyRegisteredMenus']);

    Route::post('/send-invoice/{emp_id}', [MailController::class, 'sendInvoice']);//send mail to each
    //For all employee
    Route::post('/invoices/send-all', [MailController::class, 'sendAllMonthlyInvoices']);
});

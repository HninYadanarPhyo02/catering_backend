<?php

namespace App\Http\Controllers\Api\Auth;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\InvoiceDetail;

use App\Models\FoodMonthPrice;
use App\Models\RegisteredOrder;
use function Pest\Laravel\json;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceResource;

class AttendanceController extends Controller
{
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'emp_id' => 'required|string',
    //         'date' => 'required|date',
    //         'status' => 'required|string',
    //         'check_out' => 'required|boolean',
    //     ]);

    //     // Find the foodmonthprice record by date
    //     $foodPrice = FoodMonthPrice::where('date', $request->date)->first();

    //     if (!$foodPrice) {
    //         return response()->json(['message' => 'No food price found for the given date'], 404);
    //     }

    //     // Check if attendance record exists for emp_id and date
    //     $attendance = Attendance::where('emp_id', $request->emp_id)
    //         ->where('date', $request->date)
    //         ->first();

    //     if ($attendance) {
    //         // Update attendance record
    //         if ($request->input('check_out')) {
    //             // Only allow check_out update if not already checked out
    //             if ($attendance->check_out) {
    //                 return response()->json([
    //                     'message' => 'Employee already checked out on this date',
    //                     'code' => 400
    //                 ], 400);
    //             }
    //             $attendance->status = 'present';  // convert status to present
    //             $attendance->check_out = 1;
    //         } else {
    //             $attendance->status = $request->status;
    //             $attendance->check_out = $request->input('check_out');
    //         }

    //         $attendance->save();

    //         return response()->json([
    //             'message' => 'Attendance updated successfully',
    //             'data' => $attendance,
    //             'code' => 200
    //         ]);
    //     } else {
    //         // Create new attendance record
    //         $data = Attendance::create([
    //             'emp_id' => $request->emp_id,
    //             'food_id' => $foodPrice->food_id,
    //             'date' => $request->date,
    //             'status' => $request->status,
    //             'check_out' => $request->input('check_out'),
    //         ]);

    //         return response()->json([
    //             'message' => 'Attendance recorded successfully',
    //             'data' => $data,
    //             'code' => 200
    //         ]);
    //     }
    // }
    public function store(Request $request)
{
    $request->validate([
        'emp_id' => 'required|string',
        'date' => 'required|date',
        'status' => 'required|string',
        'check_out' => 'required|boolean',
    ]);

    // Get food price
    $foodPrice = FoodMonthPrice::where('date', $request->date)->first();
    if (!$foodPrice) {
        return response()->json(['message' => 'No food price found for the given date'], 404);
    }

    // ===== ATTENDANCE =====
    $attendance = Attendance::where('emp_id', $request->emp_id)
        ->where('date', $request->date)
        ->first();

    if ($attendance) {
        if ($request->check_out) {
            if ($attendance->check_out) {
                return response()->json(['message' => 'Employee already checked out'], 400);
            }
            $attendance->status = 'present';
            $attendance->check_out = 1;
        } else {
            $attendance->status = $request->status;
            $attendance->check_out = $request->check_out;
        }
        $attendance->save();
    } else {
        $attendance = Attendance::create([
            'emp_id' => $request->emp_id,
            'food_id' => $foodPrice->food_id,
            'date' => $request->date,
            'status' => $request->status,
            'check_out' => $request->check_out,
        ]);
    }

    // ===== INVOICE DETAIL =====
    $invoice = Invoice::where('emp_id', $request->emp_id)
        ->where('month', date('n', strtotime($request->date)))
        ->where('year', date('Y', strtotime($request->date)))
        ->first();

    if (!$invoice) {
        return response()->json(['message' => 'Invoice not found for this employee and month'], 404);
    }

    $invoiceDetail = InvoiceDetail::where('invoice_id', $invoice->invoice_id)
        ->where('date', $request->date)
        ->first();

    if ($invoiceDetail) {
        if ($request->check_out) {
            if ($invoiceDetail->check_out) {
                return response()->json(['message' => 'Invoice already checked out'], 400);
            }
            $invoiceDetail->status = 'present';
            $invoiceDetail->check_out = 1;
        } else {
            $invoiceDetail->status = $request->status;
            $invoiceDetail->check_out = $request->check_out;
        }
        $invoiceDetail->save();
    } else {
        InvoiceDetail::create([
            'invoice_id' => $invoice->invoice_id,
            'date' => $request->date,
            'food_name' => $foodPrice->food_name,
            'price' => $foodPrice->price,
            'status' => $request->status,
            'check_out' => $request->check_out,
        ]);
    }

    return response()->json([
        'message' => 'Attendance and Invoice Detail saved/updated successfully',
        'attendance' => $attendance,
        'code' => 200
    ]);
}


    public function show($emp_id)
    {
        $data = Attendance::findByEmpId($emp_id);
        if ($data) {
            $data = new AttendanceResource($data);
            return response([
                'message' => 'Success',
                'data' => $data
            ], 200);
        } else {
            return response([
                'message' => 'Your emp_id is empty, try again!',
                'data' => $data,
            ], 404);
        }
    }
    public function getOrdersByToken(Request $request)
    {
        // $user = auth()->users();
        $user = $request->user();


        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // $orders = RegisteredOrder::where('emp_id', $user->emp_id)->get();
        $orders = RegisteredOrder::with(['employee', 'foodMonthPricesByDate'])
            ->where('emp_id', $user->emp_id)
            ->get();

        return response()->json([
            'message' => 'Success',
            'data' => $orders,
        ], 200);
    }


    // public function list()
    // {
    //     $data = Attendance::with('employee')->get();

    //     if ($data->isNotEmpty()) {
    //         return response([
    //             'message' => 'Success',
    //             'data' => AttendanceResource::collection($data),
    //         ], 200);
    //     }

    //     return response([
    //         'message' => 'Data is empty',
    //         'data' => [],
    //     ], 200);
    // }
    //Correct Code for employee
    public function list(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $user = $request->user();
        $empIdFilter = $user ? $user->emp_id : null;

        $query = Attendance::with(['employee', 'foodmonthpriceByDate'])
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear);

        if ($empIdFilter) {
            // Filter for current user only
            $query->where('emp_id', $empIdFilter);
        }

        $attendances = $query->orderBy('emp_id')->get();

        if ($attendances->isEmpty()) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'No attendance records found for this month.',
                'data' => [],
            ], 200);
        }

        if ($empIdFilter) {
            // Return single user attendance summary (like getAttendanceByUser)
            $totalAmount = 0;
            $dates = [];

            foreach ($attendances as $attendance) {
                $food = $attendance->foodmonthpriceByDate;
                $price = optional($food)->price ?? 0;
                $totalAmount += $price;

                $dates[] = [
                    'date' => $attendance->date,
                    'food_name' => optional($food)->food_name,
                    'price' => $price,
                    'status' => $attendance->status,
                    'check_out' => $attendance->check_out,
                ];
            }

            return response()->json([
                'isSuccess' => true,
                'message' => 'Attendance records fetched successfully for current user',
                'data' => [
                    'emp_id' => $empIdFilter,
                    'emp_name' => optional($attendances->first()->employee)->name,
                    'total_amount' => $totalAmount,
                    'attendances' => $dates,
                ],
            ]);
        } else {
            // Return grouped data for all employees
            $grouped = $attendances->groupBy('emp_id');

            $result = [];

            foreach ($grouped as $empId => $records) {
                $employee = $records->first()->employee;
                $empName = optional($employee)->name;
                $empEmail = optional($employee)->email;

                $totalAmount = 0;
                $dates = [];

                foreach ($records as $attendance) {
                    $food = $attendance->foodmonthpriceByDate;
                    $price = optional($food)->price ?? 0;
                    $totalAmount += $price;

                    $dates[] = [
                        'date' => $attendance->date,
                        'food_name' => optional($food)->food_name,
                        'price' => $price,
                        'status' => $attendance->status,
                        'check_out' => $attendance->check_out,
                    ];
                }

                $result[] = [
                    'emp_id' => $empId,
                    'emp_name' => $empName,
                    'emp_email' => $empEmail,
                    'total_amount' => $totalAmount,
                    'attendances' => $dates,
                ];
            }

            return response()->json([
                'isSuccess' => true,
                'message' => 'Monthly employee attendance totals',
                'data' => $result
            ], 200);
        }
    }

    //invoice info for Admin Role
   public function invoice(Request $request)
{
    $currentMonth = Carbon::now()->month;
    $currentYear = Carbon::now()->year;

    // Load attendance with employee and food info
    $attendances = Attendance::with(['employee', 'foodmonthpriceByDate'])
        ->whereMonth('date', $currentMonth)
        ->whereYear('date', $currentYear)
        ->orderBy('emp_id')
        ->get();

    if ($attendances->isEmpty()) {
        return response()->json([
            'isSuccess' => false,
            'message' => 'No attendance records found for this month.',
            'data' => [],
        ], 200);
    }

    $grouped = $attendances->groupBy('emp_id');

    $result = [];

    foreach ($grouped as $empId => $records) {
        $employee = $records->first()->employee;
        $empName = optional($employee)->name;
        $empEmail = optional($employee)->email; // Optional: more info
        // $empDepartment = optional($employee)->department; // Optional

        $totalAmount = 0;
        $dates = [];

        foreach ($records as $attendance) {
            $food = $attendance->foodmonthpriceByDate;
            $price = optional($food)->price ?? 0;
            $totalAmount += $price;

            $dates[] = [
                'date' => $attendance->date,
                'food_name' => optional($food)->food_name,
                'price' => $price,
                'status' => $attendance->status,
                'check_out' => $attendance->check_out,
            ];
        }

        $result[] = [
            'emp_id'       => $empId,
            'emp_name'     => $empName,
            'emp_email'    => $empEmail,
            // 'department'   => $empDepartment,
            'total_amount' => $totalAmount,
            'attendances'  => $dates,
        ];
    }

    return response()->json([
        'isSuccess' => true,
        'message' => 'Monthly employee attendance totals',
        'data' => $result
    ], 200);
}


    public function destroy($id)
    {
        $id = Attendance::find($id);
        if (!$id) {
            return response()->json([
                'message' => 'Id not found',
                ''
            ], 404);
        }
        $id->delete();
        return response()->json([
            'message' => 'Successfully deleted',
        ], 200);
    }
}

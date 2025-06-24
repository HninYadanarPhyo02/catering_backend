<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\Invoice;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\InvoiceDetail;
use App\Models\FoodMonthPrice;
use App\Models\RegisteredOrder;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoicesResource;
use Illuminate\Support\Facades\Validator;

class RegisteredOrderController extends Controller
{
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'emp_id' => 'required|exists:employee,emp_id',
    //         'date' => 'required|array|min:1',
    //         'date.*' => 'required|date',
    //     ]);

    //     $employee = $request->user();
    //     $emp_id = $request->emp_id ?? $employee->emp_id;

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $orders = [];

    //     DB::beginTransaction();
    //     try {

    //         foreach ($request->date as $date) {
    //             // 1. Register Order
    //             $registeredOrder = RegisteredOrder::register($emp_id, $date);

    //             // 2. Find matching food price
    //             $foodPrice = FoodMonthPrice::where('date', $date)->first();
    //             if (!$foodPrice) {
    //                 throw new \Exception("No food price found for date {$date}");
    //             }

    //             // 3. Create Attendance
    //             Attendance::create([
    //                 'emp_id' => $emp_id,
    //                 'food_id' => $foodPrice->food_id,
    //                 'date' => $date,
    //                 'status' => $request->input('status', 'absent'),
    //                 'check_out' => $request->input('check_out') ?? false,
    //             ]);

    //             $orders[] = $registeredOrder;
    //         }

    //         // Now generate monthly invoices AFTER processing attendance/orders
    //         $month = now()->month;
    //         $year = now()->year;

    //         $employees = Employee::all();
    //         $generatedInvoices = [];

    //         foreach ($employees as $employee) {
    //             // Find or create invoice for employee for this month/year
    //             $invoice = Invoice::firstOrCreate(
    //                 [
    //                     'emp_id' => $employee->emp_id,
    //                     'month' => $month,
    //                     'year' => $year,
    //                 ],
    //                 [
    //                     'invoice_id' => 'inv_' . str_pad(
    //                         optional(Invoice::latest('id')->first())->id + 1 ?? 1,
    //                         4,
    //                         '0',
    //                         STR_PAD_LEFT
    //                     ),
    //                     'total_amount' => 0,
    //                 ]
    //             );

    //             // Get attendances with food info for this employee/month
    //             $attendances = DB::table('attendance')
    //                 ->join('foodmonthprice', 'attendance.date', '=', 'foodmonthprice.date')
    //                 ->where('attendance.emp_id', $employee->emp_id)
    //                 ->whereMonth('attendance.date', $month)
    //                 ->whereYear('attendance.date', $year)
    //                 ->whereNull('attendance.deleted_at')
    //                 ->whereNull('foodmonthprice.deleted_at')
    //                 ->select(
    //                     'attendance.date',
    //                     'attendance.status',
    //                     'attendance.check_out',
    //                     'foodmonthprice.food_name',
    //                     'foodmonthprice.price'
    //                 )
    //                 ->get();

    //             if ($attendances->isEmpty()) {
    //                 continue;
    //             }

    //             foreach ($attendances as $att) {
    //                 // Only insert if date doesn't already exist in invoice_details
    //                 $exists = DB::table('invoice_details')
    //                     ->where('invoice_id', $invoice->invoice_id)
    //                     ->where('date', $att->date)
    //                     ->exists();

    //                 if (!$exists) {
    //                     $invoice->details()->create([
    //                         'date' => $att->date,
    //                         'food_name' => $att->food_name,
    //                         'price' => $att->price ?? 0,
    //                         'status' => $att->status,
    //                         'check_out' => (bool) $att->check_out,
    //                     ]);
    //                 }
    //             }

    //             // Recalculate total_amount
    //             $total = DB::table('invoice_details')
    //                 ->where('invoice_id', $invoice->invoice_id)
    //                 ->sum('price');

    //             $invoice->update(['total_amount' => $total]);

    //             $generatedInvoices[] = $invoice;
    //         }

    //         DB::commit();

    //         // Fetch invoices by month/year columns, NOT created_at
    //         $data = Invoice::with('details')
    //             ->where('month', $month)
    //             ->where('year', $year)
    //             ->get();

    //         return response()->json([
    //             'message' => 'All dates processed successfully and monthly invoices generated.',
    //             'successful_orders' => $orders,
    //             'invoices' => InvoicesResource::collection($data),
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'message' => 'An error occurred. All changes rolled back.',
    //             'error' => $e->getMessage()
    //         ], 409);
    //     }
    // }
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'emp_id' => 'required|exists:employee,emp_id',
        'date' => 'required|array|min:1',
        'date.*' => 'required|date',
    ]);

    $employee = $request->user();
    $emp_id = $request->emp_id ?? $employee->emp_id;

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $orders = [];

    DB::beginTransaction();
    try {
        // Step 1: Create Attendance and Registered Orders
        foreach ($request->date as $date) {
            $registeredOrder = RegisteredOrder::register($emp_id, $date);

            $foodPrice = FoodMonthPrice::where('date', $date)->first();
            if (!$foodPrice) {
                throw new \Exception("No food price found for date {$date}");
            }

            Attendance::create([
                'emp_id' => $emp_id,
                'food_id' => $foodPrice->food_id,
                'date' => $date,
                'status' => $request->input('status', 'absent'),
                'check_out' => $request->input('check_out') ?? false,
            ]);

            $orders[] = $registeredOrder;
        }

        // Step 2: Generate invoices for all months with attendance
        $employees = Employee::all();
        $generatedInvoices = [];

        foreach ($employees as $employee) {
            // Get all unique month/year combinations from attendance
            $monthYears = Attendance::where('emp_id', $employee->emp_id)
                ->whereNull('deleted_at')
                ->selectRaw('MONTH(date) as month, YEAR(date) as year')
                ->distinct()
                ->get();

            foreach ($monthYears as $my) {
                $month = $my->month;
                $year = $my->year;

                $invoice = Invoice::firstOrCreate(
                    [
                        'emp_id' => $employee->emp_id,
                        'month' => $month,
                        'year' => $year,
                    ],
                    [
                        'invoice_id' => 'inv_' . str_pad(
                            optional(Invoice::latest('id')->first())->id + 1 ?? 1,
                            4,
                            '0',
                            STR_PAD_LEFT
                        ),
                        'total_amount' => 0,
                    ]
                );

                $attendances = DB::table('attendance')
                    ->join('foodmonthprice', 'attendance.date', '=', 'foodmonthprice.date')
                    ->where('attendance.emp_id', $employee->emp_id)
                    ->whereMonth('attendance.date', $month)
                    ->whereYear('attendance.date', $year)
                    ->whereNull('attendance.deleted_at')
                    ->whereNull('foodmonthprice.deleted_at')
                    ->select(
                        'attendance.date',
                        'attendance.status',
                        'attendance.check_out',
                        'foodmonthprice.food_name',
                        'foodmonthprice.price'
                    )
                    ->get();

                foreach ($attendances as $att) {
                    $exists = DB::table('invoice_details')
                        ->where('invoice_id', $invoice->invoice_id)
                        ->where('date', $att->date)
                        ->exists();

                    if (!$exists) {
                        $invoice->details()->create([
                            'date' => $att->date,
                            'food_name' => $att->food_name,
                            'price' => $att->price ?? 0,
                            'status' => $att->status,
                            'check_out' => (bool) $att->check_out,
                        ]);
                    }
                }

                // Update total amount
                $total = DB::table('invoice_details')
                    ->where('invoice_id', $invoice->invoice_id)
                    ->sum('price');

                $invoice->update(['total_amount' => $total]);

                $generatedInvoices[] = $invoice;
            }
        }

        DB::commit();

        // Fetch all invoices with details
        $data = Invoice::with('details')->get();

        return response()->json([
            'message' => 'All dates processed successfully and monthly invoices generated.',
            'successful_orders' => $orders,
            'invoices' => InvoicesResource::collection($data),
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'An error occurred. All changes rolled back.',
            'error' => $e->getMessage(),
        ], 409);
    }
}


    public function lists()
    {
        // Eager load employee and foodmonthprice relations
        $orders = RegisteredOrder::with(['employee', 'foodMonthPricesByDate'])->get();

        $data = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'date' => $order->date,
                'emp_id' => $order->emp_id,
                'emp_name' => optional($order->employee)->name,
                'food_name' => optional($order->foodMonthPricesByDate->first())->food_name,
                'price'     => optional($order->foodMonthPricesByDate->first())->price,
                // 'price' => $order->foodmonthprice->price ?? null,
                // add more fields if needed
            ];
        });

        return response()->json([
            'isSuccess' => true,
            'message' => 'Registered Order List',
            'data' => $data
        ], 200);
    }

    public function showFoodPricesByDate($emp_id)
    {
        // Get all orders for the employee, ordered by date descending
        $orders = RegisteredOrder::with('employee')
            ->where('emp_id', $emp_id)
            ->orderBy('date', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found for this employee.'], 404);
        }

        // Map orders to include food prices for each order date
        $result = $orders->map(function ($order) {
            $availableFoods = FoodMonthPrice::whereDate('date', $order->date)
                ->with('foodMenu')
                ->get();

            return [
                'order_id' => $order->id,
                'date' => $order->date,
                'employee' => $order->employee->name ?? null,
                // 'employee' => [
                //     'name' => $order->employee->name ?? null,
                //     'emp_id' => $order->employee->emp_id ?? null,
                //     'email' => $order->employee->email ?? null,
                // ],
                'food_prices' => $availableFoods->map(function ($food) {
                    return [
                        'food_name' => $food->foodMenu->food_name ?? $food->food_name,
                        'price' => $food->price,
                        'date' => $food->date,
                    ];
                }),
            ];
        });

        return response()->json($result);
    }
}

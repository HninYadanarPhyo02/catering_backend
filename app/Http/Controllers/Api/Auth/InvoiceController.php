<?php

namespace App\Http\Controllers\Api\Auth;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\InvoiceDetail;
use App\Models\FoodMonthPrice;
use App\Mail\MonthlyReportMail;
use App\Models\RegisteredOrder;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\InvoicesResource;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceController extends Controller
{

    // public function index()
    // {
    //     $data = Invoice::get();
    //     if ($data) {
    //         $data = InvoicesResource::collection($data);
    //     }
    //     return response([
    //         'isSuccess' => true,
    //         'message' => 'Success',
    //         'data' => $data,
    //     ], 200);
    // }
    public function show($invoice_id)
    {
        $invoice = Invoice::with(['employee', 'details'])->findOrFail($invoice_id);

        return response()->json([
            'data' => $invoice,
        ]);
    }
    // public function generateMonthlyInvoices()
    // {
    //     $month = now()->month;
    //     $year = now()->year;

    //     $employees = Employee::all();
    //     $generatedInvoices = [];

    //     foreach ($employees as $employee) {
    //         // Find or create the invoice
    //         $invoice = Invoice::firstOrCreate(
    //             [
    //                 'emp_id' => $employee->emp_id,
    //                 'month' => $month,
    //                 'year' => $year,
    //             ],
    //             [
    //                 'invoice_id' => 'inv_' . str_pad(
    //                     optional(Invoice::latest('id')->first())->id + 1 ?? 1,
    //                     4,
    //                     '0',
    //                     STR_PAD_LEFT
    //                 ),
    //                 'total_amount' => 0, // Temporary, will be recalculated later
    //             ]
    //         );

    //         // Get all attendances with food info for this employee and month
    //         $attendances = DB::table('attendance')
    //             ->join('foodmonthprice', 'attendance.date', '=', 'foodmonthprice.date')
    //             ->where('attendance.emp_id', $employee->emp_id)
    //             ->whereMonth('attendance.date', $month)
    //             ->whereYear('attendance.date', $year)
    //             ->whereNull('attendance.deleted_at')
    //             ->whereNull('foodmonthprice.deleted_at')
    //             ->select(
    //                 'attendance.date',
    //                 'attendance.status',
    //                 'attendance.check_out',
    //                 'foodmonthprice.food_name',
    //                 'foodmonthprice.price'
    //             )
    //             ->get();

    //         if ($attendances->isEmpty()) {
    //             continue;
    //         }

    //         foreach ($attendances as $att) {
    //             // Only insert if date doesn't already exist in invoice_details
    //             $exists = DB::table('invoice_details')
    //                 ->where('invoice_id', $invoice->invoice_id)
    //                 ->where('date', $att->date)
    //                 ->exists();

    //             if (!$exists) {
    //                 $invoice->details()->create([
    //                     'date' => $att->date,
    //                     'food_name' => $att->food_name,
    //                     'price' => $att->price ?? 0,
    //                     'status' => $att->status,
    //                     'check_out' => $att->check_out,
    //                 ]);
    //             }
    //         }

    //         // Recalculate total
    //         $total = DB::table('invoice_details')
    //             ->where('invoice_id', $invoice->invoice_id)
    //             ->sum('price');

    //         $invoice->update(['total_amount' => $total]);

    //         $generatedInvoices[] = $invoice;
    //     }

    //     // Return updated invoice data
    //     $data = Invoice::with('details')->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();

    //     return response()->json([
    //         'message' => 'Monthly invoices processed successfully (new & updated).',
    //         'invoices' => InvoicesResource::collection($data),
    //     ], 200);
    // }

    public function generateInvoiceForLoggedInEmployee(Request $request)
    {
        $employee = $request->user(); // assuming this returns employee model or has emp_id property

        $month = now()->month;
        $year = now()->year;

        // Check if invoice already exists
        $existingInvoice = Invoice::where('emp_id', $employee->emp_id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($existingInvoice) {
            return response()->json([
                'message' => 'Invoice already generated for this month.',
                'invoice' => new InvoicesResource($existingInvoice->load('details'))
            ], 200);
        }

        // Get attendances joined with food prices
        $attendances = DB::table('attendance')
            ->join('foodmonthprice', function ($join) {
                $join->on('attendance.date', '=', 'foodmonthprice.date');
            })
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

        if ($attendances->isEmpty()) {
            return response()->json([
                'message' => 'No attendance or food data found for this month.'
            ], 404);
        }

        // Calculate total
        $totalAmount = $attendances->sum(fn($a) => $a->price ?? 0);

        // Generate invoice_id
        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $nextNum = $lastInvoice ? intval(substr($lastInvoice->invoice_id, 4)) + 1 : 1;
        $invoiceId = 'inv_' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        // Create invoice
        $invoice = Invoice::create([
            'invoice_id' => $invoiceId,
            'emp_id' => $employee->emp_id,
            'month' => $month,
            'year' => $year,
            'total_amount' => $totalAmount,
        ]);

        // Create invoice details
        foreach ($attendances as $attendance) {
            InvoiceDetail::create([
                'invoice_id' => $invoiceId,
                'date' => $attendance->date,
                'food_name' => $attendance->food_name,
                'price' => $attendance->price ?? 0,
                'status' => $attendance->status,
                'check_out' => $attendance->check_out,
            ]);
        }

        $invoice->load('details');

        return response()->json([
            'message' => 'Invoice generated successfully.',
            'invoice' => new InvoicesResource($invoice)
        ], 201);
    }

    public function generateMonthlyInvoices()
    {
        $employees = Employee::all();
        $month = now()->month;
        $year = now()->year;

        $invoices = [];

        foreach ($employees as $employee) {
            if ($employee->role === 'admin') {
                continue; // Skip admin and continue to next employee
            }

            // Check if invoice already exists for this employee this month/year
            $existingInvoice = Invoice::where('emp_id', $employee->emp_id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            if ($existingInvoice) {
                // Skip if invoice already exists
                continue;
            }

            // Get attendances joined with food prices
            $attendances = DB::table('attendance')
                ->join('foodmonthprice', function ($join) {
                    $join->on('attendance.date', '=', 'foodmonthprice.date');
                })
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

            if ($attendances->isEmpty()) {
                // No attendance data for this employee, skip to next
                continue;
            }

            // Calculate total amount
            $totalAmount = $attendances->sum(fn($a) => $a->price ?? 0);

            // Generate new invoice id
            $lastInvoice = Invoice::orderBy('id', 'desc')->first();
            $nextNum = $lastInvoice ? intval(substr($lastInvoice->invoice_id, 4)) + 1 : 1;
            $invoiceId = 'inv_' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

            // Create invoice
            $invoice = Invoice::create([
                'invoice_id' => $invoiceId,
                'emp_id' => $employee->emp_id,
                'month' => $month,
                'year' => $year,
                'total_amount' => $totalAmount,
            ]);

            // Create invoice details
            foreach ($attendances as $attendance) {
                InvoiceDetail::create([
                    'invoice_id' => $invoiceId,
                    'date' => $attendance->date,
                    'food_name' => $attendance->food_name,
                    'price' => $attendance->price ?? 0,
                    'status' => $attendance->status,
                    'check_out' => $attendance->check_out,
                ]);
            }

            $invoices[] = $invoice->load('details');
        }

        return response()->json([
            'message' => 'Invoices generated for all eligible employees.',
            'invoices' => $invoices
        ]);
    }

    // public function index()
    // {
    //     $invoices = Invoice::with(['employee', 'details'])->get();

    //     $result = $invoices->map(function ($invoice) {
    //         return [
    //             'emp_id' => optional($invoice->employee)->emp_id,
    //             'emp_name' => optional($invoice->employee)->name,
    //             'emp_email' => optional($invoice->employee)->email,
    //             'total_amount' => $invoice->total_amount,
    //             'attendances' => $invoice->details->map(function ($d) {
    //                 return [
    //                     'date' => $d->date,
    //                     'food_name' => $d->food_name,
    //                     'price' => $d->price,
    //                     'status' => $d->status,
    //                     'check_out' => $d->check_out,
    //                 ];
    //             }),
    //         ];
    //     });


    //     return response()->json($result);
    // }
//     public function index()
// {
//     $invoices = Invoice::with(['employee', 'details'])->get();

//     $result = $invoices->map(function ($invoice) {
//         // Filter valid attendances
//         $validDetails = $invoice->details->filter(function ($d) {
//             return is_null($d->deleted_at);
//         });

//         $recalculatedTotal = $validDetails->sum('price');

//         return [
//             'emp_id'       => optional($invoice->employee)->emp_id,
//             'emp_name'     => optional($invoice->employee)->name,
//             'emp_email'    => optional($invoice->employee)->email,
//             'total_amount' => $recalculatedTotal,
//             'attendances'  => $invoice->details->map(function ($d) {
//                 return [
//                     'date'       => $d->date,
//                     'food_name'  => $d->food_name,
//                     'price'      => $d->price,
//                     'status'     => $d->status,
//                     'check_out'  => $d->check_out,
//                 ];
//             }),
//         ];
//     });

//     return response()->json($result);
// }
public function index()
{
    $invoices = Invoice::with(['employee', 'details'])->get();

    $result = $invoices->map(function ($invoice) {
        // Filter out soft-deleted details
        $validDetails = $invoice->details->filter(function ($d) {
            return is_null($d->deleted_at);
        });

        $recalculatedTotal = $validDetails->sum('price');

        // Skip invoices with total_amount == 0
        if ($recalculatedTotal == 0) {
            return null;
        }

        return [
            'emp_id'       => optional($invoice->employee)->emp_id,
            'emp_name'     => optional($invoice->employee)->name,
            'emp_email'    => optional($invoice->employee)->email,
            'total_amount' => $recalculatedTotal,
            'attendances'  => $validDetails->map(function ($d) {
                return [
                    'date'      => $d->date,
                    'food_name' => $d->food_name,
                    'price'     => $d->price,
                    'status'    => $d->status,
                    'check_out' => $d->check_out,
                ];
            })->values(),
        ];
    })->filter()->values(); // Remove null entries and reset index

    return response()->json($result);
}

//INvoice for only one
public function recalculateInvoiceTotal($invoice_id)
    {
        $invoice = Invoice::with('details')->where('invoice_id', $invoice_id)->first();

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found.'], 404);
        }

        $total = $invoice->details
            ->filter(fn($d) => is_null($d->deleted_at))
            ->sum('price');

        $invoice->total_amount = $total;
        $invoice->save();

        return response()->json([
            'message' => 'Invoice total recalculated successfully.',
            'invoice_id' => $invoice->invoice_id,
            'total_amount' => $invoice->total_amount,
        ]);
    }

    //Invoice for all

    public function recalculateAllInvoices()
{
    $invoices = Invoice::with('details')->get();

    foreach ($invoices as $invoice) {
        $total = $invoice->details
            ->filter(fn($d) => is_null($d->deleted_at))
            ->sum('price');

        $invoice->total_amount = $total;
        $invoice->save();
    }

    return response()->json([
        'message' => 'All invoice totals recalculated successfully.',
        'count' => $invoices->count(),
    ]);
}

}

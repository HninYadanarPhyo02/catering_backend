<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\FoodMonthPrice;
use App\Mail\MonthlyReportMail;
use App\Models\RegisteredOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Pagination\LengthAwarePaginator;


class InvoicesController extends Controller
{
    // public function index()
    // {
    //     $employees = Employee::all();
    //     $invoices = Invoice::with('employee')->paginate(10);
    //     return view('invoices.index', compact('invoices', 'employees'));
    // }
    //     public function index(Request $request)
    // {
    //     $user = Auth::user();

    //     // Only allow admin
    //     if (!$user || $user->role !== 'admin') {
    //         abort(403, 'Unauthorized');
    //     }

    //     $currentMonth = Carbon::now()->month;
    //     $currentYear = Carbon::now()->year;

    //     // Get all attendances with relations for current month
    //     $attendances = Attendance::with(['employee', 'foodmonthpriceByDate'])
    //         ->whereMonth('date', $currentMonth)
    //         ->whereYear('date', $currentYear)
    //         ->orderBy('emp_id')
    //         ->get();

    //     if ($attendances->isEmpty()) {
    //         return view('invoices.index')->with('message', 'No attendance records found for this month.');
    //     }

    //     $grouped = $attendances->groupBy('emp_id');

    //     $invoices = [];

    //     foreach ($grouped as $empId => $records) {
    //         $employee = $records->first()->employee;
    //         $empName = optional($employee)->name;
    //         $empEmail = optional($employee)->email;

    //         $totalAmount = 0;
    //         $dates = [];

    //         foreach ($records as $attendance) {
    //             $food = $attendance->foodmonthpriceByDate;
    //             $price = optional($food)->price ?? 0;
    //             $totalAmount += $price;

    //             $dates[] = [
    //                 'date'      => $attendance->date,
    //                 'food_name' => optional($food)->food_name,
    //                 'price'     => $price,
    //                 'status'    => $attendance->status,
    //                 'check_out' => $attendance->check_out,
    //             ];
    //         }

    //         $invoices[] = [
    //             'emp_id'       => $empId,
    //             'emp_name'     => $empName,
    //             'emp_email'    => $empEmail,
    //             'total_amount' => $totalAmount,
    //             'attendances'  => $dates,
    //         ];
    //     }

    //     // Manually paginate the result
    //     $page = $request->get('page', 1);
    //     $perPage = 5;
    //     $offset = ($page - 1) * $perPage;

    //     $paginatedInvoices = new LengthAwarePaginator(
    //         array_slice($invoices, $offset, $perPage),
    //         count($invoices),
    //         $perPage,
    //         $page,
    //         ['path' => $request->url(), 'query' => $request->query()]
    //     );

    //     return view('invoices.index', [
    //         'invoices' => $paginatedInvoices,
    //         'message' => null
    //     ]);
    // }

    public function index(Request $request)
    {
        $employees = Employee::all();

        // Get filter inputs
        $empId = $request->input('emp_id');
        $month = $request->input('month');
        $year = $request->input('year');

        // Base query for attendance (for main data)
        $query = Attendance::with(['employee', 'foodmonthpriceByDate'])
            ->whereNull('deleted_at');


        if ($empId) {
            $query->where('emp_id', $empId);
        }
        if ($month) {
            $query->whereMonth('date', $month);
        }
        if ($year) {
            $query->whereYear('date', $year);
        }

        $attendances = $query->orderBy('emp_id')->orderBy('date')->get();

        // Get distinct months available in attendance data (without month/year filter, to show all possible options)
        $months = Attendance::selectRaw('MONTH(date) as month')
            ->distinct()
            ->orderBy('month')
            ->pluck('month');

        // Get distinct years available in attendance data
        $years = Attendance::selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year');

        if ($attendances->isEmpty()) {
            return view('invoices.index', compact('employees', 'months', 'years'))
                ->with('message', 'No attendance records found for the selected filters.');
        }

        // Group by employee
        $grouped = $attendances->groupBy('emp_id');

        $result = [];
        foreach ($grouped as $empId => $records) {
            $employee = $records->first()->employee;
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
                'emp_name' => optional($employee)->name,
                'emp_email' => optional($employee)->email,
                'department' => optional($employee)->department,
                'total_amount' => $totalAmount,
                'attendances' => $dates,
            ];
        }

        // Manual pagination for $result array
        $perPage = 2;
        $page = $request->input('page', 1);
        $items = collect($result);

        $paginated = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('invoices.index', [
            'invoices' => $paginated,
            'employees' => $employees,
            'months' => $months,
            'years' => $years,
            'filters' => ['emp_id' => $empId, 'month' => $month, 'year' => $year],
        ]);
    }

    public function generateInvoice(Request $request)
    {
        $type = $request->input('type', 'month');
        $emp_id = $request->input('emp_id');

        // Date range filter
        switch ($type) {
            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
            default:
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
        }

        $orders = RegisteredOrder::where('emp_id', $emp_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'No registered orders found.');
        }

        $attendanceCount = Attendance::where('emp_id', $emp_id)
            ->where('check_out', 1)
            ->whereBetween('date', [$startDate, $endDate])
            ->count();

        $foodPrice = FoodMonthPrice::where('food_id', $emp_id)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->first();

        $unit_price = $foodPrice ? $foodPrice->price : 0;
        $total = $attendanceCount * $unit_price;

        $employee = Employee::find($emp_id);

        $invoice = Invoice::create([
            'invoice_number' => (string) Str::uuid(),
            'total_day' => $attendanceCount,
            'emp_id' =>  $employee->emp_id,
            'customer_name' => $employee ? $employee->name : 'Unknown',
            'order_details' => json_encode($orders->map(fn($order) => [
                'order_id' => $order->id,
                'item' => $order->item_name,
                'date' => $order->date,
                'quantity' => $order->quantity,
            ])),
            'quantity' => $attendanceCount,
            'unit_price' => $unit_price,
            'total' => $total,
            'payment_status' => 'unpaid',
        ]);
        return redirect()->route('payments.create', ['invoiceId' => $invoice->id]);
    }

    public function create($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        return view('payments.create', compact('invoice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'customer_name' => 'required|string|max:255',
            'order_details' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'payment_status' => 'required|in:paid,unpaid,pending',
        ]);

        $validated['total'] = $validated['quantity'] * $validated['unit_price'];

        Invoice::create($validated);

        return redirect()->route('dashboard')->with('success', 'Invoice created successfully.');
    }

    // public function show($invoice_id)
    // {
    //     $invoice = Invoice::with(['employee', 'details'])->where('invoice_id', $invoice_id)->firstOrFail();
    //     return view('invoices.show', compact('invoice'));
    // }
    public function show($invoice_id)
    {
        $invoice = Invoice::with(['employee', 'details'])
            ->where('invoice_id', $invoice_id)
            ->firstOrFail();

        return view('invoices.show', compact('invoice'));
    }
    public function edit(Invoice $invoice)
    {
        return view('invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'customer_name' => 'required|string|max:255',
            'order_details' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'payment_status' => 'required|in:paid,unpaid,pending',
        ]);

        $validated['total'] = $validated['quantity'] * $validated['unit_price'];

        $invoice->update($validated);

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }
    public function sendInvoiceMail($invoice_id)
    {
        // Fetch invoice with relations
        $invoice = Invoice::with('employee', 'details')->where('invoice_id', $invoice_id)->first();

        if (!$invoice) {
            return back()->with('error', 'Invoice not found.');
        }

        if (!$invoice->employee) {
            return back()->with('error', 'Employee not found.');
        }

        // Calculate order_count if not present
        $invoice->order_count = $invoice->details->count();

        // Calculate total if not present (adjust 'amount' field to your details column)
        if (!isset($invoice->total)) {
            $invoice->total = $invoice->total_amount;
        }

        try {
            Mail::to($invoice->employee->email)
                ->send(new MonthlyReportMail($invoice, $invoice->details));

            return back()->with('success', 'Invoice email sent successfully to ' . $invoice->employee->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
    public function sendAllMonthlyInvoices(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Get all employees who have invoices for this month/year
        $employees = Employee::whereHas('invoices', function ($query) use ($currentMonth, $currentYear) {
            $query->where('month', $currentMonth)
                ->where('year', $currentYear);
        })->get();

        $sent = 0;
        $failed = [];

        foreach ($employees as $employee) {
            // Get latest invoice for current month/year with details
            $invoice = $employee->invoices()
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->with('details')
                ->latest()
                ->first();

            // Skip if no invoice or details
            if (!$invoice || $invoice->details->isEmpty()) {
                continue;
            }

            // Calculate order count and total if needed for the mail view
            $invoice->order_count = $invoice->details->count();
            $invoice->total = $invoice->total_amount; // adjust field if needed

            try {
                Mail::to($employee->email)->send(new MonthlyReportMail($invoice, $invoice->details));
                $sent++;
            } catch (\Exception $e) {
                $failed[] = $employee->emp_id . ' - ' . $e->getMessage();
            }
        }

        if (count($failed) > 0) {
            return back()->with('error', 'Failed to send email(s) to: ' . implode(', ', $failed) . '. Successfully sent: ' . $sent);
        }

        return back()->with('success', 'Monthly invoices sent successfully to ' . $sent . ' employees.');
    }
}

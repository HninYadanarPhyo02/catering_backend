<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\InvoiceDetail;
use App\Mail\MonthlyReportMail;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    //Send Mail to each
    // public function sendInvoice(Request $request, $emp_id)
    // {
    //     try {
    //         // Find employee by emp_id
    //         $employee = Employee::where('emp_id', $emp_id)->first();

    //         if (!$employee) {
    //             return response()->json(['message' => 'Employee not found'], 404);
    //         }

    //         // Find latest invoice for the employee
    //         $invoice = Invoice::where('emp_id', $emp_id)
    //             ->latest()
    //             ->first();

    //         if (!$invoice) {
    //             return response()->json(['message' => 'No invoice found for this employee'], 404);
    //         }

    //         // Get invoice details
    //         $details = InvoiceDetail::where('invoice_id', $invoice->id)->get();

    //         // Send the email to the employee's email address
    //         Mail::to($employee->email)->send(new MonthlyReportMail($invoice, $details));

    //         return response()->json(['message' => 'Invoice sent successfully to ' . $employee->email]);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Failed to send invoice: ' . $e->getMessage()], 500);
    //     }
    // }

    public function sendInvoice($emp_id)
    {
        // Find employee by emp_id
        $employee = Employee::where('emp_id', $emp_id)->first();

        if (!$employee) {
            return back()->with('error', 'Employee not found.');
        }

        // Fetch the latest invoice with details and employee relation
        $invoice = Invoice::with('employee', 'details')
            ->where('emp_id', $emp_id)
            ->latest()
            ->first();

        if (!$invoice) {
            return back()->with('error', 'Invoice not found for this employee.');
        }

        // Count of orders (details)
        $invoice->order_count = $invoice->details->count();

        // Sum total amount from details (adjust 'amount' to your field name)
        $invoice->total = $invoice->total_amount;

        try {
            Mail::to($invoice->employee->email)
                ->send(new MonthlyReportMail($invoice, $invoice->details));

            return back()->with('success', 'Invoice email sent successfully to ' . $invoice->employee->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }


    //For all controller
    public function sendAllMonthlyInvoices()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Get all employees who have an invoice for this month
        $employees = Employee::whereHas('invoices', function ($query) use ($currentMonth, $currentYear) {
            $query->where('month', $currentMonth)
                ->where('year', $currentYear);
        })->get();

        $sent = 0;
        $failed = [];

        foreach ($employees as $employee) {
            // Get latest invoice for this month
            $invoice = $employee->invoices()
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->with('details')
                ->latest()
                ->first();

            if (!$invoice || $invoice->details->isEmpty()) {
                continue;
            }

            // Calculate order count and total (if needed)
            $invoice->order_count = $invoice->details->count();
            $invoice->total = $invoice->total_amount; // adjust 'amount' to your column name

            try {
                Mail::to($employee->email)->send(new MonthlyReportMail($invoice, $invoice->details));
                $sent++;
            } catch (\Exception $e) {
                $failed[] = $employee->emp_id . ' - ' . $e->getMessage();
            }
        }

        return response()->json([
            'message' => "Monthly invoices sent.",
            'sent_count' => $sent,
            'failed' => $failed,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\RegisteredOrder;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::orderBy('created_at', 'desc')->paginate(10);
        $order = Attendance::where('check_out', 1)->get();
        $invoice = Invoice::latest()->first();
        // dd($invoice);
        return view('payment', compact('payments', 'order', 'invoice'));
    }


    public function create(Invoice $invoice) // ✅ Automatically resolved by Laravel
    {
        return view('payments.create', compact('invoice'));
    }


    public function store(Request $request)
    {
        // $invoice = Invoice::findOrFail($request->input('invoice_id'));
        $invoice = Invoice::where('invoice_number', $request->input('invoice_number'))->firstOrFail();


        // Save payment record (optional)
        Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $invoice->total,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Update invoice
        $invoice->payment_status = 'paid';
        $invoice->save();

        return redirect()->route('invoices.index')->with('success', 'Payment recorded successfully.');
    }
    public function processPayment(Request $request, $invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        $rules = [
            'payment_method' => 'nullable|string|max:50', // e.g. cash, card, paypal
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->total_due,
            'payment_date' => 'required|date',
            'reference' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Payment::create([
            'invoice_id' => $invoice->id,
            'payment_date' => $request->payment_date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'reference' => $request->reference,
        ]);

        // You can update invoice payment status or amount paid here if needed

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Payment recorded successfully!');
    }
}

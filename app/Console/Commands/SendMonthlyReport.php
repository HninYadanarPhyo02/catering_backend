<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Employee;
use App\Mail\MonthlyReportMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendMonthlyReport extends Command
{
    protected $signature = 'invoice:send-monthly';
    protected $description = 'Send monthly invoice to employees';

    public function handle()
    {
        $month = now()->month;
        $year = now()->year;

        $invoices = Invoice::where('month', $month)
            ->where('year', $year)
            ->get();

        foreach ($invoices as $invoice) {
            $employee = Employee::find($invoice->emp_id);

            // Assuming you store details as related or JSON
            $details = json_decode($invoice->details); // or use relation

            if ($employee && $employee->email) {
                Mail::to($employee->email)->send(new MonthlyReportMail($invoice, $details));
                $this->info("Invoice sent to {$employee->email}");
            }
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\Invoice;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\InvoiceDetail;
use App\Models\FoodMonthPrice;
use App\Models\RegisteredOrder;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class HolidaysController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $holidaysCount = Holiday::whereYear('date', $now->year)->count();
        $now = Carbon::now();
        $holidaysCount = Holiday::whereYear('date', $now->year)->count();
        $holidays = Holiday::orderBy('created_at', 'desc')->paginate(4);
        return view('holidays', compact('holidays', 'holidaysCount'));
        return view('holidays', compact('holidays', 'holidaysCount'));
    }
    // public function store(Request $request)
    // {
    //     // Step 1: Generate new h_id
    //     // Step 1: Generate new h_id
    //     $lastHoliday = Holiday::orderByRaw("CAST(SUBSTRING(h_id, 6) AS UNSIGNED) DESC")->first();
    //     $lastIdNum = $lastHoliday ? intval(substr($lastHoliday->h_id, 5)) : 0;
    //     $newhId = 'holi_' . str_pad($lastIdNum + 1, 3, '0', STR_PAD_LEFT);

    //     // Step 2: Validate form input
    //     $lastIdNum = $lastHoliday ? intval(substr($lastHoliday->h_id, 5)) : 0;
    //     $newhId = 'holi_' . str_pad($lastIdNum + 1, 3, '0', STR_PAD_LEFT);

    //     // Step 2: Validate form input
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'date' => 'required|date|unique:holidays,date',
    //         'description' => 'nullable|string|max:1000',
    //     ]);

    //     $date = $validated['date'];

    //     // Step 3: Create the holiday record
    //     $date = $validated['date'];

    //     // Step 3: Create the holiday record
    //     Holiday::create([
    //         'h_id' => $newhId,
    //         'name' => $validated['name'],
    //         'date' => $date,
    //         'description' => $validated['description'] ?? null,
    //         'name' => $validated['name'],
    //         'date' => $date,
    //         'description' => $validated['description'] ?? null,
    //     ]);

    //     // Step 4: Delete related data on the same date
    //     RegisteredOrder::whereDate('date', $date)->delete();
    //     Attendance::whereDate('date', $date)->delete();
    //     FoodMonthPrice::whereDate('date', $date)->delete();
    //     InvoiceDetail::whereDate('date', $date)->delete(); // ← Fixed here

    //     // Optional: delete Feedback if related to date
    //     // Feedback::whereDate('created_at', $date)->delete();

    //     // Step 5: Redirect with success message
    //     return redirect()->back()->with('success', 'Holiday added and related records deleted successfully.');

    //     // Step 4: Delete related data on the same date
    //     RegisteredOrder::whereDate('date', $date)->delete();
    //     Attendance::whereDate('date', $date)->delete();
    //     FoodMonthPrice::whereDate('date', $date)->delete();
    //     InvoiceDetail::whereDate('date', $date)->delete(); // ← Fixed here

    //     // Optional: delete Feedback if related to date
    //     // Feedback::whereDate('created_at', $date)->delete();

    //     // Step 5: Redirect with success message
    //     return redirect()->back()->with('success', 'Holiday added and related records deleted successfully.');
    // }
    public function store(Request $request)
    {
        // Step 1: Generate new h_id
        $lastHoliday = Holiday::orderByRaw("CAST(SUBSTRING(h_id, 6) AS UNSIGNED) DESC")->first();
        $lastIdNum = $lastHoliday ? intval(substr($lastHoliday->h_id, 5)) : 0;
        $newhId = 'holi_' . str_pad($lastIdNum + 1, 3, '0', STR_PAD_LEFT);

        // Step 2: Validate form input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|unique:holidays,date',
            'description' => 'nullable|string|max:1000',
        ]);

        $date = $validated['date'];

        // Step 3: Create the holiday record
        Holiday::create([
            'h_id' => $newhId,
            'name' => $validated['name'],
            'date' => $date,
            'description' => $validated['description'] ?? null,
        ]);

        // Step 4: Delete related data on the same date
        RegisteredOrder::whereDate('date', $date)->delete();
        Attendance::whereDate('date', $date)->delete();
        FoodMonthPrice::whereDate('date', $date)->delete();

        $affectedInvoiceDetails = InvoiceDetail::whereDate('date', $validated['date'])->get();
        $affectedInvoiceIds = $affectedInvoiceDetails->pluck('invoice_id')->unique();

        InvoiceDetail::whereDate('date', $validated['date'])->delete();

        // 5. Recalculate total_amount for affected invoices
        foreach ($affectedInvoiceIds as $invoiceId) {
            $newTotal = InvoiceDetail::where('invoice_id', $invoiceId)
                ->whereNull('deleted_at')
                ->sum('price');

            Invoice::where('invoice_id', $invoiceId)
                ->update(['total_amount' => $newTotal]);
        }

        // Step 6: Redirect back with success message
        return redirect()->back()->with('success', 'Holiday added and related records updated successfully.');
    }


    // public function update(Request $request, $h_id)
    // {
    //     $holiday = Holiday::where('h_id', $h_id)->first();

    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'date' => 'required|date|unique:holidays,date,' . $h_id . ',h_id',
    //         'description' => 'required|string|max:1000',
    //     ]);

    //     $holiday->update([
    //         'name' => $request->name,
    //         'date' => $request->date,
    //         'description' => $request->description,
    //     ]);


    //     return redirect()->route('holidays')->with('success', 'Customer updated successfully.');
    // }
    public function update(Request $request, $h_id)
    {
        $holiday = Holiday::where('h_id', $h_id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|unique:holidays,date,' . $h_id . ',h_id',
            'description' => 'nullable|string|max:1000', // made nullable here
        ]);

        $holiday->update([
            'name' => $request->name,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        return redirect()->route('holidays')->with('success', 'Holiday updated successfully.');
    }



    public function edit($h_id)
    {
        $holiday = Holiday::where('h_id', $h_id)->firstOrFail();
        return view('holidays.edit', compact('holiday'));
    }

    public function destroy($h_id)
    {
        $holiday = Holiday::where('h_id', $h_id)->firstOrFail();
        $holiday->delete();

        return redirect()->back()->with('success', 'Holiday deleted successfully.');
    }

    public function uploadExcel(Request $request)
    {
        // Validate file
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        // Load file
        $file = $request->file('excel_file');

        // Read Excel (you need Maatwebsite Excel package installed)
        $rows = Excel::toArray([], $file);

        // Example: Assume holidays data is in the first sheet
        $dataRows = $rows[0];

        $insertedCount = 0;
        foreach ($dataRows as $index => $row) {
            // Skip header row if present (e.g., index 0)
            if ($index === 0) {
                // Optionally check headers to verify file format
                continue;
            }

            // Assuming $row has [date, description] format
            $date = $row[0] ?? null;
            $description = $row[1] ?? null;

            // Validate date and description before insert
            if (!$date || !$description) {
                continue; // skip invalid row
            }

            // Convert Excel date format to Y-m-d if needed
            // Laravel Excel usually returns string if CSV or formatted Excel

            // Insert or update holiday
            Holiday::updateOrCreate(
                ['date' => $date],
                ['description' => $description]
            );

            $insertedCount++;
        }

        return redirect()->back()->with('success', "$insertedCount holidays imported successfully.");
    }
}

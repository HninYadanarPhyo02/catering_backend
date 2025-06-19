<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\FoodMonthPrice;
use App\Models\InvoiceDetail;
use App\Models\RegisteredOrder;
use Maatwebsite\Excel\Facades\Excel;

class HolidaysController extends Controller
{
    public function index()
    {
        // $holidays = Holiday::orderBy('date', 'asc')->get();
        $holidays = Holiday::orderBy('created_at', 'desc')->paginate(3);
        return view('holidays', compact('holidays'));
    }
    public function store(Request $request)
    {
        $lastHoliday = Holiday::orderByRaw("CAST(SUBSTRING(h_id, 6) AS UNSIGNED) DESC")->first();

        $lasthid = $lastHoliday ? intval(substr($lastHoliday->h_id, 5)) : 0; // Note: substr start at 5 (0-based index)

        $newNumber = $lasthid + 1;

        $newhId = 'holi_' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        // Validate form input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|unique:holidays,date',
            'description' => 'nullable|string|max:1000',
        ]);

        // dd([
        //     'h_id' => $newhId,
        //     'name' => $request->name,
        //     'date' => $request->date,
        //     'description' => $request->description,
        // ]);
        FoodMonthPrice::whereDate('date', $validated['date'])->delete();
        // Create the holiday
        Holiday::create([
            'h_id' => $newhId,
            'name' => $request->name,
            'date' => $request->date,
            'description' => $request->description ?? null,
        ]);
        $affectedOrders = RegisteredOrder::whereDate('date', $request->date)->get();
        $affectAttendance = Attendance::whereDate('date', $request->date)->get();
        $affectAvailableFood = FoodMonthPrice::whereDate('date', $request->date)->get();
        $affectAvailableInvoice = InvoiceDetail::whereDate('date', $request->date)->get();

        if ($affectedOrders->count()) {
            RegisteredOrder::whereDate('date',$request->date)->delete();
        }
        if ($affectAttendance->count()) {
            Attendance::whereDate('date', $request->date)->delete();
        }
        if ($affectAvailableFood->count()) {
            FoodMonthPrice::whereDate('date', $request->date)->delete();
        }
        if ($affectAvailableInvoice->count()) {
            FoodMonthPrice::whereDate('date', $request->date)->delete();
        }

        // Redirect with success message
        return redirect()->back()->with('success', 'Holiday added successfully.');
    }

    public function update(Request $request, $h_id)
    {
        $holiday = Holiday::where('h_id', $h_id)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|unique:holidays,date,' . $h_id . ',h_id',
            'description' => 'required|string|max:1000',
        ]);

        $holiday->update([
            'name' => $request->name,
            'date' => $request->date,
            'description' => $request->description,
        ]);


        return redirect()->route('holidays')->with('success', 'Customer updated successfully.');
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

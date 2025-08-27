<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Attendance;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\InvoiceDetail;
use App\Models\FoodMonthPrice;
use App\Models\RegisteredOrder;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the announcements.
     */
    public function index(Request $request)
    {
        $query = Announcement::query();

        // Search by title or text or date or message (optional)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('text', 'like', "%{$search}%")
                    ->orWhere('date', 'like', "%{$search}%");
                // ->orWhere('message', 'like', "%{$search}%"); // if you have message field
            });
        }

        // You can add date or message filters too if you want here
        // $announcements = $query->orderBy('created_at', 'desc')->paginate(6);
        $announcements = $query->orderBy('created_at', 'desc')->paginate(10);
        $now = Carbon::now();
        $monthlyAnnouncementCount = Announcement::whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->count();


        return view('announcement', compact('announcements','monthlyAnnouncementCount'));
        return view('announcement', compact('announcements','monthlyAnnouncementCount'));
    }

    public function show($id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('announcements.show', compact('announcement'));
    }

    public function create()
    {
        return view('announcements.create');
    }

    // public function store(Request $request)
    // {
    //     // 1. Validate input
    //     $validated = $request->validate([
    //         'title' => 'required|string|max:255',
    //         'date' => 'required|date',
    //         'text' => 'required|string',
    //     ]);

    //     // 2. Check if announcement already exists for the date
    //     $existingAnnouncement = Announcement::whereDate('date', $validated['date'])->first();
    //     if ($existingAnnouncement) {
    //         return redirect()->back()
    //             ->withInput()
    //             ->withErrors(['date' => 'An announcement for this date already exists. You cannot create another one.']);
    //     }

    //     // 3. Create announcement
    //     $announcement = Announcement::create($validated);

    //     // 4. Soft delete related records
    //     RegisteredOrder::whereDate('date', $validated['date'])->delete();
    //     Attendance::whereDate('date', $validated['date'])->delete();
    //     FoodMonthPrice::whereDate('date', $validated['date'])->delete();
    //     InvoiceDetail::whereDate('date', $validated['date'])->delete();

    //     // 5. Redirect back with success message
    //     return redirect()->route('announcement')
    //         ->with('success', 'Announcement successfully created. All related records on the announced date have been soft deleted.');
    // }

    public function store(Request $request)
    {
        // 1. Validate input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'text' => 'required|string',
        ]);

        // 2. Check if announcement already exists for the date
        $existingAnnouncement = Announcement::whereDate('date', $validated['date'])->first();
        if ($existingAnnouncement) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['date' => 'An announcement for this date already exists. You cannot create another one.']);
        }

        // 3. Create announcement
        $announcement = Announcement::create($validated);

        // 4. Soft delete related records
        RegisteredOrder::whereDate('date', $validated['date'])->delete();
        Attendance::whereDate('date', $validated['date'])->delete();
        FoodMonthPrice::whereDate('date', $validated['date'])->delete();

        // Soft delete invoice details and get affected invoice IDs
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

        // 6. Redirect back with success message
        return redirect()->route('announcement')
            ->with('success', 'Announcement successfully created. All related records on the announced date have been soft deleted and invoice totals updated.');
    }


    // If the admin announce something to the employee, registered_order is auto deleted
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'date' => 'required|date',
    //         'title' => 'required|string',
    //         'text' => 'required|string',
    //     ]);

    //     // Create the announcement
    //     $data = Announcement::create($validated);

    //     // Soft delete registered orders on the same date
    //     RegisteredOrder::where('date', $validated['date'])->delete();

    //     return redirect()->route('announcement')->with('success', 'Announcement created successfully.');
    // }
    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('announcements.edit', compact('announcement'));
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //         'text' => 'required|string',
    //         'date' => 'nullable|date',
    //         'message' => 'nullable|string|max:255',
    //     ]);

    //     $announcement = Announcement::findOrFail($id);
    //     $announcement->update($request->only(['title', 'text', 'date', 'message']));

    //     return redirect()->route('announcements.index')->with('success', 'Announcement updated successfully.');
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'date' => 'required|date',
            'text' => 'required|string',
        ]);

        $announcement = Announcement::findOrFail($id);
        $announcement->title = $request->title;
        $announcement->date = $request->date;
        $announcement->text = $request->text;
        $announcement->save();

        return redirect()->route('announcement')
            ->with('success', 'Announcement updated successfully.');
    }


    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->route('announcement')->with('success', 'Announcement deleted successfully.');
    }
}

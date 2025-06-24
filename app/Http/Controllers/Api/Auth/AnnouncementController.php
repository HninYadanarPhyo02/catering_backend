<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\Invoice;
use App\Models\Attendance;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\InvoiceDetail;
use App\Models\FoodMonthPrice;
use App\Models\RegisteredOrder;
use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;

class AnnouncementController extends Controller
{
    // public function create(Request $request)
    // {
    //     // 1. Validate the announcement data
    //     $validated = $request->validate([
    //         'date' => 'required|date',
    //         'title' => 'required|string|max:255',
    //         'text' => 'required|string',
    //     ]);

    //     // 2. Check if an announcement already exists for the given date
    //     $existingAnnouncement = Announcement::whereDate('date', $validated['date'])->first();
    //     if ($existingAnnouncement) {
    //         return response()->json([
    //             'isSuccess' => false,
    //             'message' => 'An announcement for this date already exists. You cannot create another one.'
    //         ], 422); // 422 Unprocessable Entity is typical for validation errors
    //     }

    //     // 3. Create the new announcement
    //     $announcement = Announcement::create($validated);

    //     // 4. Soft delete all registered orders, attendance, and food prices for that date
    //     $affectedOrders = RegisteredOrder::whereDate('date', $validated['date'])->get();
    //     $affectAttendance = Attendance::whereDate('date', $validated['date'])->get();
    //     $affectAvailableFood = FoodMonthPrice::whereDate('date', $validated['date'])->get();
    //     $affectAvailableInvoice = InvoiceDetail::whereDate('date', $validated['date'])->get();

    //     if ($affectedOrders->count()) {
    //         RegisteredOrder::whereDate('date', $validated['date'])->delete();
    //     }
    //     if ($affectAttendance->count()) {
    //         Attendance::whereDate('date', $validated['date'])->delete();
    //     }
    //     if ($affectAvailableFood->count()) {
    //         FoodMonthPrice::whereDate('date', $validated['date'])->delete();
    //     }
    //     if ($affectAvailableInvoice->count()) {
    //         InvoiceDetail::whereDate('date', $validated['date'])->delete();
    //     }

    //     // 5. Return success response
    //     return response()->json([
    //         'isSuccess' => true,
    //         'message' => 'Announcement successfully created. All related records on the announced date have been soft deleted.',
    //         'data' => $announcement,
    //         'deleted_orders_count' => $affectedOrders->count()
    //     ], 200);
    // }
    public function create(Request $request)
    {
        // 1. Validate the announcement data
        $validated = $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'text' => 'required|string',
        ]);

        // 2. Check if an announcement already exists for the given date
        $existingAnnouncement = Announcement::whereDate('date', $validated['date'])->first();
        if ($existingAnnouncement) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'An announcement for this date already exists. You cannot create another one.'
            ], 422);
        }

        // 3. Create the new announcement
        $announcement = Announcement::create($validated);

        // 4. Find all related records for the date
        $affectedOrders = RegisteredOrder::whereDate('date', $validated['date'])->get();
        $affectedAttendance = Attendance::whereDate('date', $validated['date'])->get();
        $affectedFood = FoodMonthPrice::whereDate('date', $validated['date'])->get();
        $affectedInvoices = InvoiceDetail::whereDate('date', $validated['date'])->get();

        // 5. Soft delete them
        if ($affectedOrders->isNotEmpty()) {
            RegisteredOrder::whereDate('date', $validated['date'])->delete();
        }

        if ($affectedAttendance->isNotEmpty()) {
            Attendance::whereDate('date', $validated['date'])->delete();
        }

        if ($affectedFood->isNotEmpty()) {
            FoodMonthPrice::whereDate('date', $validated['date'])->delete();
        }

        if ($affectedInvoices->isNotEmpty()) {
            InvoiceDetail::whereDate('date', $validated['date'])->delete();

            // 6. Recalculate and update invoice totals
            $invoiceIds = $affectedInvoices->pluck('invoice_id')->unique();

            foreach ($invoiceIds as $invoiceId) {
                $newTotal = InvoiceDetail::where('invoice_id', $invoiceId)
                    ->whereNull('deleted_at')
                    ->sum('price');

                Invoice::where('invoice_id', $invoiceId)
                    ->update(['total_amount' => $newTotal]);
            }
        }

        // 7. Return JSON response
        return response()->json([
            'isSuccess' => true,
            'message' => 'Announcement successfully created. All related records on the announced date have been soft deleted and invoices updated.',
            'data' => $announcement,
            'deleted_orders_count' => $affectedOrders->count()
        ]);
    }



    public function show($date)
    {
        $data = Announcement::findByDate($date);
        if ($data) {
            $data = new AnnouncementResource($data);
            return response([
                'isSuccess' => true,
                'message' => 'Success',
                'data' => $data
            ], 200);
        } else {
            return response([
                'isSuccess' => false,
                'message' => 'Data is empty',
                'data' => $data
            ], 404);
        }
    }
    public function list()
    {
        $data = Announcement::get();
        if ($data) {
            $data = AnnouncementResource::collection($data);
            return response([
                'isSuccess' => true,
                'message' => 'Success',
                'data' => $data
            ], 200);
        } else {
            return response([
                'isSuccess' =>  false,
                'message' => 'Fail',
                'data' => $data
            ], 404);
        }
    }
    public function update(Request $request, $id)
    {
        $data = Announcement::find($id);
        if (!$data) {
            return response([
                'isSuccess' => false,
                'message' => 'Id not found',
            ], 404);
        }
        $data->update([
            'date' => $request->date,
            'title' => $request->title,
            'text' => $request->text,
        ]);
        return response()->json([
            'isSuccess' => true,
            'message' => 'Data is successfully updated',
            'data' => $data
        ], 200);
    }
    public function destroy($id)
    {
        $data = Announcement::find($id);
        if (!$data) {
            return response([
                'isSuccess' => false,
                'message' => 'Id is not',
            ], 404);
        }
        $data->delete();
        return response()->json([
            'isSuccess' => true,
            'message' => 'Successfully deleted'
        ], 200);
    }
}

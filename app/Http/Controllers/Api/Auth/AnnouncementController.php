<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\FoodMonthPrice;
use App\Models\InvoiceDetail;
use App\Models\RegisteredOrder;
use Illuminate\Http\Request;

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

    //     // 2. Create the new announcement
    //     $announcement = Announcement::create($validated);

    //     // 3. Soft delete all registered orders for that date
    //     $affectedOrders = RegisteredOrder::whereDate('date', $validated['date'])->get();
    //     $affectAttendance = Attendance::whereDate('date', $validated['date'])->get();
    //     $affectAvailableFood = FoodMonthPrice::whereDate('date', $validated['date'])->get();

    //     if ($affectedOrders->count()) {
    //         RegisteredOrder::whereDate('date', $validated['date'])->delete();
    //     }
    //     if ($affectAttendance->count()) {
    //         Attendance::whereDate('date', $validated['date'])->delete();
    //     }
    //     if ($affectAvailableFood->count()) {
    //         FoodMonthPrice::whereDate('date', $validated['date'])->delete();
    //     }
    //     // Optional: you can log or broadcast to employees about deletion if needed

    //     // 4. Return JSON response
    //     return response()->json([
    //         'isSuccess' => true,
    //         'message' => 'Announcement successfully created. All registered orders on the announced date have been soft deleted.',
    //         'data' => $announcement,
    //         'deleted_orders_count' => $affectedOrders->count()
    //     ], 200);
    // }
    //     public function create(Request $request)
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

    //     if ($affectedOrders->count()) {
    //         RegisteredOrder::whereDate('date', $validated['date'])->delete();
    //     }
    //     if ($affectAttendance->count()) {
    //         Attendance::whereDate('date', $validated['date'])->delete();
    //     }
    //     if ($affectAvailableFood->count()) {
    //         FoodMonthPrice::whereDate('date', $validated['date'])->delete();
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
            ], 422); // 422 Unprocessable Entity is typical for validation errors
        }

        // 3. Create the new announcement
        $announcement = Announcement::create($validated);

        // 4. Soft delete all registered orders, attendance, and food prices for that date
        $affectedOrders = RegisteredOrder::whereDate('date', $validated['date'])->get();
        $affectAttendance = Attendance::whereDate('date', $validated['date'])->get();
        $affectAvailableFood = FoodMonthPrice::whereDate('date', $validated['date'])->get();
        $affectAvailableInvoice = InvoiceDetail::whereDate('date', $validated['date'])->get();

        if ($affectedOrders->count()) {
            RegisteredOrder::whereDate('date', $validated['date'])->delete();
        }
        if ($affectAttendance->count()) {
            Attendance::whereDate('date', $validated['date'])->delete();
        }
        if ($affectAvailableFood->count()) {
            FoodMonthPrice::whereDate('date', $validated['date'])->delete();
        }
        if ($affectAvailableInvoice->count()) {
            FoodMonthPrice::whereDate('date', $request->date)->delete();
        }

        // 5. Return success response
        return response()->json([
            'isSuccess' => true,
            'message' => 'Announcement successfully created. All related records on the announced date have been soft deleted.',
            'data' => $announcement,
            'deleted_orders_count' => $affectedOrders->count()
        ], 200);
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

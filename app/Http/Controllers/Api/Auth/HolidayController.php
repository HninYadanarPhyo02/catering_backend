<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use App\Models\Holiday;
use App\Models\Attendance;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Imports\HolidayImport;
use App\Models\FoodMonthPrice;
use App\Imports\EmployeeImport;
use App\Models\RegisteredOrder;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\FoodResource;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\HolidayResource;
use Illuminate\Support\Facades\Storage;

class HolidayController extends Controller
{
    public function store(Request $request)
    {
        $lastmenu = Holiday::orderByRaw("CAST(SUBSTRING(h_id, 6) AS UNSIGNED) DESC")->first();

        $lastNumber = $lastmenu ? intval(substr($lastmenu->food_id, 5)) : 0; // Note: substr start at 5 (0-based index)

        $newNumber = $lastNumber + 1;

        $newhId = 'holi_' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        if (!$request->name || trim($request->name) === '') {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Holiday name is empty'
            ], 404);
        }
        if (!$request->date || trim($request->date) === '') {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Date is empty'
            ], 404);
        }

        $exist = Holiday::where('date', $request->date)->first();
        if ($exist) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Date is already define',
            ], 404);
        }
        $data = Holiday::create([
            'h_id' => $newhId,
            'name' => $request->name,
            'date' => $request->date,
            'description' => $request->description ?? null,
        ]);

        // Check if date already exists in announcements
        if (Announcement::where('date', $request->date)->exists()) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Date already exists as an announcement'
            ], 404);
        }
        $affectedOrders = RegisteredOrder::whereDate('date', $request->date)->get();
        $affectAttendance = Attendance::whereDate('date', $request->date)->get();
        $affectAvailableFood = FoodMonthPrice::whereDate('date', $request->date)->get();

        if ($affectedOrders->count()) {
            RegisteredOrder::whereDate('date', $request->date)->delete();
        }
        if ($affectAttendance->count()) {
            Attendance::whereDate('date', $request->date)->delete();
        }
        if ($affectAvailableFood->count()) {
            FoodMonthPrice::whereDate('date', $request->date)->delete();
        }
        return response()->json([
            'isSuccess' => true,
            'message' => 'Data is created',
            'data' => $data
        ], 200);
    }
    public function show($date)
    {
        $data = Holiday::where('date', $date)->first();
        if ($data) {
            $data = new HolidayResource($data);
            return response([
                'message' => 'Success',
                'data' => $data,
            ], 200);
        } else {
            return response([
                'message' => 'Fail',
                'data' => $data,
            ], 404);
        }
    }
    public function list()
    {
        $data = Holiday::get();
        if ($data) {
            $data = HolidayResource::collection($data);
        }
        return response([
            'code' => 200,
            'message' => 'Success',
            'data' => $data,
        ]);
    }
    public function update(Request $request, $date)
    {
        $data = Holiday::where('date', $date)->first();
        if (!$data) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Date not found',
            ], 404);
        }
        $data->update([
            'name' => $request->name,
            'date' => $request->date,
            'description' => $request->description ?? null,
        ]);
        return response()->json([
            'isSuccess' => true,
            'message' => 'Data is updated',
            'data' => $data
        ], 200);
    }
    public function destroy($date)
    {
        $data = Holiday::where('date', $date)->first();
        if (!$data) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Date not found',
            ], 404);
        }
        $data->delete();
        return response()->json([
            'isSuccess' => true,
            'message' => 'Data is deleted',
        ], 200);
    }

    public function importBase64(Request $request)
    {
        $request->validate([
            'file_base64' => 'required|string',
            'filename' => 'required|string'
        ]);

        // Get the base64 string from the request
        $base64 = $request->input('file_base64');

        // Strip metadata prefix if present (e.g., "data:application/xyz;base64,")
        if (str_contains($base64, ',')) {
            $base64 = explode(',', $base64)[1];
        }

        // $filenameBase = preg_replace('/[^a-zA-Z0-9_\-]/', '', pathinfo($request->input('filename'), PATHINFO_FILENAME));
        // $filename = $request->input('filename') . '.xlsx';
        // $filePath = "uploads/{$filename}";

        $filenameBase = preg_replace('/[^a-zA-Z0-9_\-]/', '', pathinfo($request->input('filename'), PATHINFO_FILENAME));
        $filename = $filenameBase . '.xlsx'; // Safe and only one extension
        $filePath = "uploads/{$filename}";

        // Delete existing files with same base filename
        $files = Storage::disk('local')->files('uploads');
        foreach ($files as $file) {
            if (str_starts_with(pathinfo($file, PATHINFO_FILENAME), $filenameBase)) {
                Storage::disk('local')->delete($file);
            }
        }

        // Save the decoded file
        Storage::disk('local')->put($filePath, base64_decode($base64));

        // Import using maatwebsite/excel
        $import = new HolidayImport();
        Excel::import($import, storage_path("app/private/{$filePath}"));

        return response()->json([
            'isSuccess' => true,
            'message' => 'File uploaded and data imported successfully',
            // 'imported_count' => $import->rows->count(),
        ], 200);
    }

    // public function importBase64(Request $request)
    // {
    //     $request->validate([
    //         'file_base64' => 'required|string',
    //         'filename' => 'required|string'
    //     ]);

    //     $base64 = $request->input('file_base64');

    //     // Remove data URI scheme prefix if present
    //     if (str_contains($base64, ',')) {
    //         $base64 = explode(',', $base64)[1];
    //     }

    //     $decoded = base64_decode($base64, true);
    //     if ($decoded === false) {
    //         return response()->json([
    //             'isSuccess' => false,
    //             'message' => 'Invalid base64 data',
    //         ], 400);
    //     }

    //     // Generate safe filename
    //     $filenameBase = preg_replace('/[^a-zA-Z0-9_\-]/', '', pathinfo($request->input('filename'), PATHINFO_FILENAME));
    //     $filename = $filenameBase . '_' . time() . '.xlsx';
    //     $filePath = "uploads/{$filename}";

    //     // Delete old files with similar base name
    //     foreach (Storage::disk('local')->files('uploads') as $file) {
    //         if (str_starts_with(pathinfo($file, PATHINFO_FILENAME), $filenameBase)) {
    //             Storage::disk('local')->delete($file);
    //         }
    //     }

    //     // Save the file to storage/app/uploads
    //     Storage::disk('local')->put($filePath, $decoded);

    //     // Import using Maatwebsite Excel
    //     try {
    //         Excel::import(new HolidayImport(), storage_path("app/{$filePath}"));

    //         return response()->json([
    //             'isSuccess' => true,
    //             'message' => 'File uploaded and data imported successfully',
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'isSuccess' => false,
    //             'message' => 'Import failed: ' . $e->getMessage(),
    //         ], 400);
    //     }
    // }
}

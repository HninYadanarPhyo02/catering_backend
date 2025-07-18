<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Employee;
use App\Models\Feedback;
use App\Models\FoodMenu;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Imports\EmployeeImport;
use App\Models\RegisteredOrder;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\PasswordResource;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;



class EmployeeController extends Controller
{
    public function importAndInsert(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new EmployeeImport();
        Excel::import($import, $request->file('file')); // ✅ Correct usage

        $imported = $import->rows;

        $emails = $imported->pluck('email')->filter()->unique();
        $existing = Employee::whereIn('email', $emails)->pluck('email')->toArray();
        $newRows = $imported->filter(fn($row) => !in_array($row['email'], $existing));

        foreach ($newRows as $row) {
            Employee::create([
                'emp_id' => $row['emp_id'],
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => Hash::make('emp123'),
            ]);
        }

        return response()->json([
            'inserted_count' => $newRows->count(),
            'message' => 'New employee records inserted successfully.',
        ]);
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

        $filenameBase = preg_replace('/[^a-zA-Z0-9_\-]/', '', pathinfo($request->input('filename'), PATHINFO_FILENAME));
        $filename = $request->input('filename') . '.xlsx';
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
        $import = new EmployeeImport();
        Excel::import($import, storage_path("app/private/{$filePath}"));

        return response()->json([
            'isSuccess' => true,
            'message' => 'File uploaded and data imported successfully',
            // 'imported_count' => $import->rows->count(),
        ], 200);
    }
    public function list()
    {
        $data = Employee::get();
        if ($data) {
            $data = EmployeeResource::collection($data);
        }
        return response([
            'isSuccess' => true,
            'message' => 'Success',
            'data' => $data,
        ], 200);
    }
    public function showInfo($emp_id)
    {

        // $data = FoodMenu::where('name', $name)->first();
        $data = Employee::where('emp_id', $emp_id)->first();
        //   dd($data);

        if ($data) {
            $data = new EmployeeResource($data);
            return response([
                'message' => 'Success',
                'data' => $data,
            ], 200);
        } else {
            return response([
                'message' => 'Data not found with {$name}',
                'data' => $data,
            ], 404);
        }
    }
    public function showPsw($emp_id)
    {

        // $data = FoodMenu::where('name', $name)->first();
        $data = Employee::where('emp_id', $emp_id)->first();
        //   dd($data);

        if ($data) {
            $data = new PasswordResource($data);
            return response([
                'message' => 'Success',
                'data' => $data,
            ], 200);
        } else {
            return response([
                'message' => 'Data not found with {$name}',
                'data' => $data,
            ], 404);
        }
    }
    public function updateInfo(Request $request, $emp_id)
    {
        $employee = Employee::where('emp_id', $emp_id)->firstOrFail();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:employee,email,' . $employee->emp_id . ',emp_id',
        ]);

        $employee->name  = $request->name;
        $employee->email = $request->email;

        $employee->save();

        return response()->json([
            'message' => 'Employee updated successfully.',
            'employee' => new EmployeeResource($employee),
        ]);
    }
    public function updatePsw(Request $request, $emp_id)
    {
        $employee = Employee::where('emp_id', $emp_id)->firstOrFail();

        $request->validate([
            'old_password'     => 'required|string',
            'new_password'     => 'required|string|min:6',
            'confirm_password' => 'required|string|same:new_password',
        ]);

        if (!Hash::check($request->old_password, $employee->password)) {
            throw ValidationException::withMessages([
                'old_password' => ['Old password is incorrect.'],
            ]);
        }

        $employee->password = Hash::make($request->new_password);
        $employee->save();

        return response()->json([
            'message' => 'Password updated successfully.',
        ], 200);
    }
    // public function updateforAdmin(Request $request, $admin_id)
    // {
    //     $employee = Employee::where('emp_id', $admin_id)->first();

    //     if (!$employee) {
    //         return response()->json(['message' => 'Employee not found'], 404);
    //     }

    //     $request->validate([
    //         'name'  => 'required|string|max:255',
    //         'email' => 'required|email|unique:employee,email,' . $employee->emp_id . ',emp_id',
    //         'role'  => 'required|in:admin,employee',
    //     ]);

    //     $newRole = $request->role;
    //     $roleChanged = $newRole !== $employee->role;

    //     // If role changed, delete associated data and regenerate emp_id and password
    //     if ($roleChanged) {
    //         // Delete associated records using the old emp_id
    //         RegisteredOrder::where('emp_id', $employee->emp_id)->delete();
    //         Attendance::where('emp_id', $employee->emp_id)->delete();
    //         Feedback::where('emp_id', $employee->emp_id)->delete();
    //         Invoice::where('emp_id', $employee->emp_id)->delete();

    //         if ($newRole === 'admin') {
    //             $lastAdmin = Employee::where('role', 'admin')
    //                 ->where('emp_id', 'like', 'admin_%')
    //                 ->orderByRaw("CAST(SUBSTRING(emp_id, 7) AS UNSIGNED) DESC")
    //                 ->first();

    //             $lastNumber = $lastAdmin ? intval(substr($lastAdmin->emp_id, 6)) : 0;
    //             $employee->emp_id = 'admin_' . str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
    //             $employee->password = Hash::make('admin123');
    //         } else {
    //             $lastEmp = Employee::where('role', 'employee')
    //                 ->where('emp_id', 'like', 'emp_%')
    //                 ->orderByRaw("CAST(SUBSTRING(emp_id, 5) AS UNSIGNED) DESC")
    //                 ->first();

    //             $lastNumber = $lastEmp ? intval(substr($lastEmp->emp_id, 4)) : 0;
    //             $employee->emp_id = 'emp_' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    //             $employee->password = Hash::make('emp123');
    //         }
    //     } else {
    //         // Optional password change if old, new, and confirmation are all present
    //         if (
    //             $request->filled('old_password') &&
    //             $request->filled('new_password') &&
    //             $request->filled('new_password_confirmation')
    //         ) {
    //             if (!Hash::check($request->old_password, $employee->password)) {
    //                 return response()->json(['message' => 'Old password is incorrect'], 403);
    //             }

    //             if ($request->new_password !== $request->new_password_confirmation) {
    //                 return response()->json(['message' => 'New password confirmation does not match'], 422);
    //             }

    //             $employee->password = Hash::make($request->new_password);
    //         }
    //     }

    //     // Update common fields
    //     $employee->name  = $request->name;
    //     $employee->email = $request->email;
    //     $employee->role  = $newRole;

    //     $employee->save();

    //     return response()->json([
    //         'message' => 'Employee updated successfully',
    //         'employee' => $employee,
    //     ], 200);
    // }

    public function updateforAdmin(Request $request, $admin_id)
{
    $employee = Employee::where('emp_id', $admin_id)->first();

    if (!$employee) {
        return response()->json(['message' => 'Employee not found'], 404);
    }

    $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email|unique:employee,email,' . $employee->id,
        'role'  => 'required|in:admin,employee',
    ]);

    $newRole = $request->role;
    $roleChanged = $newRole !== $employee->role;

    // If role changed, delete associated records and generate new emp_id + password
    if ($roleChanged) {
        // 🔥 DELETE associated records if changing from employee to admin
        if ($employee->role === 'employee' && $newRole === 'admin') {
            DB::table('attendance')->where('emp_id', $employee->emp_id)->delete();
            DB::table('registered_order')->where('emp_id', $employee->emp_id)->delete();
            DB::table('feedback')->where('emp_id', $employee->emp_id)->delete();
        }

        // 🔐 Generate new emp_id and reset password
        if ($newRole === 'admin') {
            $lastAdmin = Employee::where('role', 'admin')
                ->where('emp_id', 'like', 'admin_%')
                ->orderByRaw("CAST(SUBSTRING(emp_id, 7) AS UNSIGNED) DESC")
                ->first();

            $lastNumber = $lastAdmin ? intval(substr($lastAdmin->emp_id, 6)) : 0;
            $employee->emp_id = 'admin_' . str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
            $employee->password = Hash::make('admin123');
        } else {
            $lastEmp = Employee::where('role', 'employee')
                ->where('emp_id', 'like', 'emp_%')
                ->orderByRaw("CAST(SUBSTRING(emp_id, 5) AS UNSIGNED) DESC")
                ->first();

            $lastNumber = $lastEmp ? intval(substr($lastEmp->emp_id, 4)) : 0;
            $employee->emp_id = 'emp_' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $employee->password = Hash::make('emp123');
        }
    } else {
        // ✅ Optional password update
        if (
            $request->filled('old_password') &&
            $request->filled('new_password') &&
            $request->filled('new_password_confirmation')
        ) {
            if (!Hash::check($request->old_password, $employee->password)) {
                return response()->json(['message' => 'Old password is incorrect'], 403);
            }

            if ($request->new_password !== $request->new_password_confirmation) {
                return response()->json(['message' => 'New password confirmation does not match'], 422);
            }

            $employee->password = Hash::make($request->new_password);
        }
    }

    // ✏️ Update other fields
    $employee->name = $request->name;
    $employee->email = $request->email;
    $employee->role = $newRole;

    $employee->save();

    return response()->json([
        'message' => 'Employee updated successfully',
        'employee' => $employee,
    ], 200);
}



    public function destroy($emp_id)
    {
        $data = Employee::where('emp_id', $emp_id)->first();
        // $food = FoodMenu::where('name', $food_name)->first();
        if (!$data) {
            return response()->json(
                [
                    'isSuccess' => false,
                    'message' => 'EmployeeId not found'
                ],
                404
            );
        }
        $affectedAttendances = Attendance::where('emp_id', $emp_id)->get();
        $affectedRegisteredOrder = RegisteredOrder::where('emp_id', $emp_id)->get();
        $affectedInvoice = Invoice::where('emp_id', $emp_id)->get();
        $affectedFeedback = Feedback::where('emp_id', $emp_id)->get();

        if ($affectedRegisteredOrder->count()) {
            RegisteredOrder::where('emp_id', $emp_id)->delete();
        }
        if ($affectedInvoice->count()) {
            Invoice::where('emp_id', $emp_id)->delete();
        }
        if ($affectedAttendances->count()) {
            Attendance::where('emp_id', $emp_id)->delete();
        }
        if ($affectedFeedback->count()) {
            Feedback::where('emp_id', $emp_id)->delete();
        }
        $data->delete();

        return response()->json(['message' => 'Employee deleted'], 200);
    }
    public function getEmployeeAttendance($emp_id)
    {
        $employee = Employee::with('attendances')->where('emp_id', $emp_id)->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json([
            'employee' => $employee->name, // use actual field name in DB
            'attendance' => $employee->attendances,
        ]);
    }
}

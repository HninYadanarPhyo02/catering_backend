<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Employee;
use App\Models\Feedback;
use App\Models\Attendance;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\CustomerImport;
use App\Imports\EmployeeImport;
use App\Models\RegisteredOrder;
use PhpParser\Node\Expr\Empty_;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class CustomersController extends Controller
{

    // public function index(Request $request)
    // {
    //     $query = Employee::query();

    //     if ($request->filled('role')) {
    //         $query->where('role', $request->role);
    //     }
    //     $employeeCount = Employee::where('role', 'employee')->count();
    //     $adminCount = Employee::where('role', 'admin')->count();

    //     $customers = $query->orderBy('created_at', 'desc')->paginate(5);

    //     return view('customers.index', compact('customers','employeeCount','adminCount'));
    // }
    public function index(Request $request)
{
    $query = Employee::query();

    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }

    $employeeCount = Employee::where('role', 'employee')->count();
    $adminCount = Employee::where('role', 'admin')->count();

    $customers = $query->orderBy('created_at', 'desc')->paginate(10);

    // If AJAX request, return only the table partial + counts as JSON
    if ($request->ajax()) {
        $table = view('customers.partials.table', compact('customers'))->render();

        return response()->json([
            'table' => $table,
            'counts' => [
                'employee' => $employeeCount,
                'admin' => $adminCount,
            ]
        ]);
    }

    // Normal full page load
    return view('customers.index', compact('customers','employeeCount','adminCount'));
}



    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        // Check if email (even soft deleted) exists
        $existingEmployee = Employee::withTrashed()->where('email', $request->email)->first();

        if ($existingEmployee) {
            if ($existingEmployee->trashed()) {
                $existingEmployee->restore();

                return redirect()->route('customers.index')->with('success', 'Employee restored successfully.');
            }

            return redirect()->back()->with('error', 'Email is already in use.');
        }

        // Generate unique emp_id (e.g., emp_0001)
        $newNumber = 1;
        do {
            $newEmpId = 'emp_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            $exists = Employee::withTrashed()->where('emp_id', $newEmpId)->exists();
            $newNumber++;
        } while ($exists);

        // Create employee
        Employee::create([
            'id' => (string) Str::uuid(),
            'emp_id' => $newEmpId,
            'name'  => $request->name,
            'email' => $request->email,
            'password' => Hash::make('emp123'),
        ]);

        return redirect()->route('customers.index')->with('success', 'New customer is added successfully.');
    }

    

    public function edit($emp_id)
    {
        $customer = Employee::where('emp_id', $emp_id)->firstOrFail();
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $emp_id)
    {
        $employee = Employee::where('emp_id', $emp_id)->firstOrFail();
    {
        $employee = Employee::where('emp_id', $emp_id)->firstOrFail();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:employee,email,' . $employee->id,
            'role'  => 'required|in:admin,employee',
        ]);
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:employee,email,' . $employee->id,
            'role'  => 'required|in:admin,employee',
        ]);

        $newRole = $validated['role'];
        $roleChanged = $newRole !== $employee->role;
        $newRole = $validated['role'];
        $roleChanged = $newRole !== $employee->role;

        $updateData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $newRole,
        ];
        $updateData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $newRole,
        ];

        if ($roleChanged) {
            // Delete related records if changing from employee to admin
            if ($employee->role === 'employee' && $newRole === 'admin') {
                DB::table('attendance')->where('emp_id', $employee->emp_id)->delete();
                DB::table('registered_order')->where('emp_id', $employee->emp_id)->delete();
                DB::table('feedback')->where('emp_id', $employee->emp_id)->delete();
            }
        if ($roleChanged) {
            // Delete related records if changing from employee to admin
            if ($employee->role === 'employee' && $newRole === 'admin') {
                DB::table('attendance')->where('emp_id', $employee->emp_id)->delete();
                DB::table('registered_order')->where('emp_id', $employee->emp_id)->delete();
                DB::table('feedback')->where('emp_id', $employee->emp_id)->delete();
            }

            // Generate new emp_id based on new role
            if ($newRole === 'admin') {
                $lastAdmin = Employee::where('role', 'admin')
                    ->where('emp_id', 'like', 'admin_%')
                    ->orderByRaw("CAST(SUBSTRING(emp_id, 7) AS UNSIGNED) DESC")
                    ->first();
            // Generate new emp_id based on new role
            if ($newRole === 'admin') {
                $lastAdmin = Employee::where('role', 'admin')
                    ->where('emp_id', 'like', 'admin_%')
                    ->orderByRaw("CAST(SUBSTRING(emp_id, 7) AS UNSIGNED) DESC")
                    ->first();

                $lastNumber = $lastAdmin ? intval(substr($lastAdmin->emp_id, 6)) : 0;
                $newNumber = $lastNumber + 1;
                $newEmpId = 'admin_' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
                $employee->password = Hash::make('admin123');
            } else {
                $lastEmp = Employee::where('role', 'employee')
                    ->where('emp_id', 'like', 'emp_%')
                    ->orderByRaw("CAST(SUBSTRING(emp_id, 5) AS UNSIGNED) DESC")
                    ->first();
                $lastNumber = $lastAdmin ? intval(substr($lastAdmin->emp_id, 6)) : 0;
                $newNumber = $lastNumber + 1;
                $newEmpId = 'admin_' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
                $employee->password = Hash::make('admin123');
            } else {
                $lastEmp = Employee::where('role', 'employee')
                    ->where('emp_id', 'like', 'emp_%')
                    ->orderByRaw("CAST(SUBSTRING(emp_id, 5) AS UNSIGNED) DESC")
                    ->first();

                $lastNumber = $lastEmp ? intval(substr($lastEmp->emp_id, 4)) : 0;
                $newNumber = $lastNumber + 1;
                $newEmpId = 'emp_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
                $employee->password = Hash::make('emp123');
            }
                $lastNumber = $lastEmp ? intval(substr($lastEmp->emp_id, 4)) : 0;
                $newNumber = $lastNumber + 1;
                $newEmpId = 'emp_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
                $employee->password = Hash::make('emp123');
            }

            $updateData['emp_id'] = $newEmpId;
        }
            $updateData['emp_id'] = $newEmpId;
        }

        $employee->update($updateData);
        $employee->update($updateData);

        return redirect()->route('customers.index')->with('success', 'Employee updated successfully.');
    }
        return redirect()->route('customers.index')->with('success', 'Employee updated successfully.');
    }




    public function destroy($emp_id)
    {
        $customer = Employee::where('emp_id', $emp_id)->first();
        // $customer = Employee::findOrFail($emp_id);
        $name = $customer->name;
        $customer->delete();
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

        return redirect()->route('customers.index')->with('success', "$name has been deleted.");
    }


    public function import(Request $request)
    {
        // 1. Validate uploaded file (allow xlsx, xls, csv, ods)
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,ods|max:2048',
        ]);

        // 2. Get the uploaded file
        $file = $request->file('excel_file');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Invalid or no file uploaded.');
        }

        // Optional: debug info
        // dd([
        //    'originalName' => $file->getClientOriginalName(),
        //    'mimeType' => $file->getMimeType(),
        //    'extension' => $file->getClientOriginalExtension(),
        //    'size' => $file->getSize(),
        // ]);

        try {
            // 3. Import the Excel file using your CustomerImport class
            dd($file->getClientOriginalName());
            Excel::import(new CustomerImport, $file);
            // Excel::import(new CustomerImport(), $file->getRealPath());
            // dd($file);

            return redirect()->back()->with('success', 'Customers imported successfully.');
        } catch (\Exception $e) {
            Log::error('Excel Import Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\CustomerImport;
use App\Imports\EmployeeImport;
use PhpParser\Node\Expr\Empty_;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\Validator;

class CustomersController extends Controller
{

    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(5);

        return view('customers.index', compact('customers'));
    }


    // public function index()
    // {
    //     // Fetch customers with pagination (10 per page)
    //     $customers = Employee::orderBy('created_at', 'desc')->paginate(5);

    //     // Pass them to the view
    //     return view('customers.index', compact('customers'));
    // }

    public function store(Request $request)
    {
        // dd($request->all());
        $lastId = Employee::orderByRaw("CAST(SUBSTRING(emp_id, 5) AS UNSIGNED) DESC")->first();
        $lastNumber = $lastId ? intval(substr($lastId->emp_id, 4)) : 0;
        $newNumber = $lastNumber + 1;
        $newEmpId = 'emp_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // $lastEmployee = Employee::orderBy('created_at', 'desc')->first();
        // $lastEmpId = $lastEmployee ? intval(substr($lastEmployee->emp_id, 4)) : 0;
        // $newEmpId = 'emp_' . str_pad($lastEmpId + 1, 4, '0', STR_PAD_LEFT);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:employee,email',
        ]);
        // if (!Employee::where('emp_id', 'emp_0001')->exists()) {
        //     Employee::create([
        //         'emp_id' => 'emp_0001',
        //         'name' => 'Michael',
        //         'email' => 'michael@gmail.com',
        //         'password' => bcrypt('your_password_here'),
        //         'id' => Str::uuid(),
        //     ]);
        // }

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


    // public function update(Request $request, Employee $customer)
    //     {
    //         dd($request->all());
    //         // $customer = Employee::findOrFail($emp_id);
    //         $customer = Employee::where('emp_id', $emp_id)->first();


    //         $request->validate([
    //             'name'  => 'required|string|max:255',
    //             'email' => 'required|email|unique:employee_table,email,' . $emp_id . ',emp_id',
    //         ]);
    //             dd('Validation passed, continuing update...');



    //         $customer->update([
    //             'name'  => $request->name,
    //             'email' => $request->email,
    //         ]);
    //         // dd($request->name);

    //     return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');

    //     }

    public function update(Request $request, $emp_id)
    {
        $employee = Employee::where('emp_id', $emp_id)->firstOrFail();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:employee,email,' . $employee->emp_id . ',emp_id',
            'role' => 'required|in:admin,employee',
        ]);

        $newRole = $request->role;

        if ($newRole === 'admin') {
            $lastAdmin = Employee::where('role', 'admin')
                ->where('emp_id', 'like', 'admin_%')
                ->orderByRaw("CAST(SUBSTRING(emp_id, 7) AS UNSIGNED) DESC")
                ->first();

            $lastNumber = $lastAdmin ? intval(substr($lastAdmin->emp_id, 6)) : 0;
            $newNumber = $lastNumber + 1;
            $newEmpId = 'admin_' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
            $newPassword = Hash::make('admin123');
        } else {
            $lastEmp = Employee::where('role', 'employee')
                ->where('emp_id', 'like', 'emp_%')
                ->orderByRaw("CAST(SUBSTRING(emp_id, 5) AS UNSIGNED) DESC")
                ->first();

            $lastNumber = $lastEmp ? intval(substr($lastEmp->emp_id, 4)) : 0;
            $newNumber = $lastNumber + 1;
            $newEmpId = 'emp_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            $newPassword = Hash::make('emp123');
        }

        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $newRole,
            'emp_id' => $newEmpId,
            'password' => $newPassword,
        ]);

        return redirect()->route('customers.index')->with('success', 'Employee updated successfully.');
    }




    public function destroy($emp_id)
    {
        $customer = Employee::where('emp_id', $emp_id)->first();
        // $customer = Employee::findOrFail($emp_id);
        $name = $customer->name;
        $customer->delete();

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

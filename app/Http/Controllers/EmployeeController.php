<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Imports\EmployeeImport;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function importAndInsert(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);
        // dd($request->file('file')->getClientOriginalName());

        $import = new EmployeeImport();
        $decoded = base64_decode($request->file('file'));
        Excel::import($import, $decoded);

        $imported = $import->rows;

        // Extract all emails from imported data
        $emails = $imported->pluck('email')->filter()->unique();

        // Find existing records by email
        $existing = Employee::whereIn('email', $emails)->pluck('email')->toArray();
        // dd($existing);
        $newRows = $imported->filter(fn($row) => !in_array($row['email'], $existing));
        // dd($newRows);
        foreach ($newRows as $row) {
            Employee::create([
                'emp_id' => $row['emp_id'],
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => Hash::make('default@123'), // Secure default password
                // 'department' => $row['department'] ?? null,
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
            'filename' => 'required|string',
        ]);

        // Decode base64 and save to temporary file
        $decoded = base64_decode($request->file_base64);
        $tmpFilePath = storage_path('app/temp/' . $request->filename);

        file_put_contents($tmpFilePath, $decoded);

        // Import the Excel file
        $import = new EmployeeImport();
        Excel::import($import, $tmpFilePath);

        // Remove the temporary file
        unlink($tmpFilePath);

        // Optional: process imported data as you like
        $imported = $import->rows;

        return response()->json([
            'message' => 'Excel data imported successfully.',
            'total_rows' => $imported->count(),
        ]);
    }
}

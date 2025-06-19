<?php

namespace App\Imports;

use Illuminate\Support\Str;
use App\Models\Employee;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImport implements ToCollection,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public Collection $rows;

    // public function __construct()
    // {
    //     $this->rows = collect();
    // }

    // public function collection(Collection $collection)
    // {
    //     // $collection->shift(); // remove header if needed
    //     // dd($collection);
        
    //     foreach ($collection as $row) {
    //     $lastEmployee = Employee::orderBy('emp_id', 'desc')->first();

    //     // If no employees yet, start from 1
    //     $lastNumber = $lastEmployee ? intval(substr($lastEmployee->emp_id, 4)) : 0;
    //     $newNumber = $lastNumber + 1;
    //     $newEmpId = 'emp_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    //         Employee::create([
                
    //             'emp_id' => $newEmpId,
    //             'name' => $row['name'],
    //             'email' => $row['email'],
    //             'password' => Hash::make('password1234'),
    //             // 'department' => $row[3] ?? null,
    //         ]);
    //     }
//     public function collection(Collection $collection)
// {
//     $lastEmployee = Employee::orderBy('emp_id', 'desc')->first();
//     $lastNumber = $lastEmployee ? intval(substr($lastEmployee->emp_id, 4)) : 0;
    

//     $employeesData = [];

//     foreach ($collection as $row) {
//         // Skip rows with missing name or email
//         if (empty($row['name']) || empty($row['email'])) {
//             continue;
//         }

//         $lastNumber++;
//         $newEmpId = 'emp_' . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);

//         $employeesData[] = [
//             'id' => (string) Str::uuid(),
//             'emp_id' => $newEmpId,
//             'name' => $row['name'],
//             'email' => $row['email'],
//             'password' => Hash::make('password123'),
//             'role' => 'employee',
//         ];
//     }

//     // Only insert if we have valid data
//     if (!empty($employeesData)) {
//         Employee::insert($employeesData);
//     }
// }


//     //     return response()->json([
//     //     'message' => 'File uploaded and data imported successfully',
//     //     'imported_count' => $collection,
//     // ]);
//     

public function collection(Collection $collection)
{
    $lastEmployee = Employee::orderBy('emp_id', 'desc')->first();
    $lastNumber = $lastEmployee ? intval(substr($lastEmployee->emp_id, 4)) : 0;

    $employeesData = [];

    // Fetch all existing emails once to avoid querying DB inside the loop
    $existingEmails = Employee::pluck('email')->map(function ($email) {
        return strtolower(trim($email));
    })->toArray();

    $seenEmails = []; // to prevent duplicates within the uploaded file

    foreach ($collection as $row) {
        $name = isset($row['name']) ? trim($row['name']) : null;
        $email = isset($row['email']) ? strtolower(trim($row['email'])) : null;

        // Skip if name/email is missing or duplicated
        if (empty($name) || empty($email) || in_array($email, $seenEmails) || in_array($email, $existingEmails)) {
            continue;
        }

        $seenEmails[] = $email;

        $lastNumber++;
        $newEmpId = 'emp_' . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);

        $employeesData[] = [
            'id' => (string) Str::uuid(),
            'emp_id' => $newEmpId,
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('emp123'),
            'role' => 'employee',
        ];
    }

    if (!empty($employeesData)) {
        Employee::insert($employeesData);

        return response()->json([
        'isSuccess' => true,
        'message' => 'Import completed.',
        'imported_count' => count($employeesData),
    ],200);
    }
    return response()->json([
        'isSuccess' => false,
        'message' => 'No valid employee data to import.'
    ], 404);
    
}
}
//     // Optional JSON response for API endpoint
//     // return response()->json([
//     //     'message' => 'File uploaded and data imported successfully',
//     //     'imported_count' => count($employeesData),
//     // ]);
// }
// 
// public function collection(Collection $collection)
// {
//     $lastEmployee = Employee::orderBy('emp_id', 'desc')->first();
//     $lastNumber = $lastEmployee ? intval(substr($lastEmployee->emp_id, 4)) : 0;

//     $employeesData = [];

//     $existingEmails = Employee::pluck('email')->map(function ($email) {
//         return strtolower(trim($email));
//     })->toArray();

//     $seenEmails = [];

//     foreach ($collection as $row) {
//         $name = isset($row['name']) ? trim($row['name']) : null;
//         $email = isset($row['email']) ? strtolower(trim($row['email'])) : null;

//         // If name or email is empty
//         if (empty($name) || empty($email)) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Name or email is missing in the uploaded file.',
//                 'row' => $row
//             ], 404);
//         }

//         // If email is duplicate in file or DB
//         if (in_array($email, $seenEmails)) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => "Duplicate email found in uploaded file: $email"
//             ], 404);
//         }

//         if (in_array($email, $existingEmails)) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => "Email already exists in database: $email"
//             ], 404);
//         }

//         $seenEmails[] = $email;

//         $lastNumber++;
//         $newEmpId = 'emp_' . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);

//         $employeesData[] = [
//             'id' => (string) Str::uuid(),
//             'emp_id' => $newEmpId,
//             'name' => $name,
//             'email' => $email,
//             'password' => Hash::make('password123'),
//             'role' => 'employee',
//         ];
//     }

//     if (!empty($employeesData)) {
//         Employee::insert($employeesData);

//         return response()->json([
//             'status' => 'success',
//             'message' => 'Employees imported successfully.',
//             'imported_count' => count($employeesData)
//         ]);
//     }

//     return response()->json([
//         'status' => 'error',
//         'message' => 'No valid employee data to import.'
//     ], 404);
// }
// }


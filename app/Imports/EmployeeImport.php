<?php

namespace App\Imports;

use Illuminate\Support\Str;
use App\Models\Employee;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class EmployeeImport implements ToCollection,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public Collection $rows;


//This is old code
// public function collection(Collection $collection)
// {
//     $lastEmployee = Employee::orderBy('emp_id', 'desc')->first();
//     $lastNumber = $lastEmployee ? intval(substr($lastEmployee->emp_id, 4)) : 0;

//     $employeesData = [];

//     // Fetch all existing emails once to avoid querying DB inside the loop
//     $existingEmails = Employee::pluck('email')->map(function ($email) {
//         return strtolower(trim($email));
//     })->toArray();

//     $seenEmails = []; // to prevent duplicates within the uploaded file

//     foreach ($collection as $row) {
//         $name = isset($row['name']) ? trim($row['name']) : null;
//         $email = isset($row['email']) ? strtolower(trim($row['email'])) : null;

//         // Skip if name/email is missing or duplicated
//         if (empty($name) || empty($email) || in_array($email, $seenEmails) || in_array($email, $existingEmails)) {
//             continue;
//         }

//         $seenEmails[] = $email;

//         $lastNumber++;
//         $newEmpId = 'emp_' . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);

//         $employeesData[] = [
//             'id' => (string) Str::uuid(),
//             'emp_id' => $newEmpId,
//             'name' => $name,
//             'email' => $email,
//             'password' => Hash::make('emp123'),
//             'role' => 'employee',
//         ];
//     }

//     if (!empty($employeesData)) {
//         Employee::insert($employeesData);

//         return response()->json([
//         'isSuccess' => true,
//         'message' => 'Import completed.',
//         'imported_count' => count($employeesData),
//     ],200);
//     }
//     return response()->json([
//         'isSuccess' => false,
//         'message' => 'No valid employee data to import.'
//     ], 404);
    
// }

//This is new code
public function collection(Collection $collection)
{
    // Get the last emp_id number, e.g., emp_0010 => 10
    $lastEmployee = Employee::withTrashed()
        ->orderByRaw("CAST(SUBSTRING(emp_id, 5) AS UNSIGNED) DESC")
        ->first();

    $lastNumber = $lastEmployee ? intval(substr($lastEmployee->emp_id, 4)) : 0;

    $employeesData = [];

    // Fetch all existing emails (including soft-deleted ones)
    $existingEmails = Employee::withTrashed()
        ->pluck('email')
        ->map(fn($email) => strtolower(trim($email)))
        ->toArray();

    $seenEmails = []; // to prevent duplicates within the file

    foreach ($collection as $row) {
        $name = isset($row['name']) ? trim($row['name']) : null;
        $email = isset($row['email']) ? strtolower(trim($row['email'])) : null;

        // Skip if name or email is missing
        if (empty($name) || empty($email)) {
            continue;
        }

        // Restore soft-deleted user if email matches
        $existing = Employee::withTrashed()->where('email', $email)->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore(); // Bring back the soft-deleted user
            }
            continue; // Skip inserting, already exists
        }

        // Skip if already seen in current import batch
        if (in_array($email, $seenEmails)) {
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
            'password' => Hash::make('emp123'), // default password
            'role' => 'employee',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // Insert new records if any
    if (!empty($employeesData)) {
        Employee::insert($employeesData);
    }
}

}



<?php

namespace App\Imports;

// use Log;
use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection; // ✅ CORRECT ONE


class CustomerImport implements ToCollection, WithHeadingRow
{
    // public function collection(Collection $rows)
    // {
    //     // dd($rows);
    //     foreach ($rows as $row) {
    //         // Debug: Log to check content
    //         // Log::info('Row:', $row->toArray());

    //         if (empty($row['name']) || empty($row['email'])) {
    //             continue;
    //         }

    //         // Skip if email exists
    //         if (Employee::where('email', $row['email'])->exists()) {
    //             continue;
    //         }

    //         $lastEmp = Employee::orderByRaw("CAST(SUBSTRING(emp_id, 5) AS UNSIGNED) DESC")->first();
    //         $lastNumber = $lastEmp ? intval(substr($lastEmp->emp_id, 4)) : 0;
    //         $newNumber = $lastNumber + 1;
    //         $newEmpId = 'emp_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    //         Employee::create([
    //             'id' => Str::uuid(),
    //             'emp_id' => $newEmpId,
    //             'name' => $row['name'],
    //             'email' => $row['email'],
    //             'password' => Hash::make('employee@123'),
    //             'role' => 'employee',
    //         ]);
    //     }
    // }
    public function collection(Collection $rows)
    {
        // dd($rows);
        foreach ($rows as $row) {
            if (!isset($row['name'], $row['email']) || empty($row['name']) || empty($row['email'])) {
                Log::info('Skipped empty row or missing fields: ' . json_encode($row));
                continue;
            }

            if (Employee::where('email', $row['email'])->exists()) {
                Log::info('Skipped existing email: ' . $row['email']);
                continue;
            }

            $lastEmp = Employee::orderByRaw("CAST(SUBSTRING(emp_id, 5) AS UNSIGNED) DESC")->first();
            $lastNumber = $lastEmp ? intval(substr($lastEmp->emp_id, 4)) : 0;
            $newNumber = $lastNumber + 1;
            $newEmpId = 'emp_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            $emp = Employee::create([
                'id' => Str::uuid(),
                'emp_id' => $newEmpId,
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => Hash::make('employee@123'),
                'role' => 'employee',
            ]);

            Log::info('Inserted employee: ' . $emp->email);
        }
    }
}

<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Holiday;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HolidayImport implements ToCollection, WithHeadingRow
{
    // public function collection(Collection $collection)
    // {
    //     $lastHoliday = Holiday::orderBy('h_id', 'desc')->first();
    //     $lastNumber = $lastHoliday ? intval(substr($lastHoliday->h_id, 2)) : 0;

    //     $HolidaysData = [];
    //     $existingDays = Holiday::pluck('date')->map(fn($date) => strtolower(trim($date)))->toArray();
    //     $seenDays = [];

    //     foreach ($collection as $row) {
    //         $name = trim($row['name'] ?? '');
    //         $date = isset($row['date']) ? \Carbon\Carbon::createFromFormat('d-m-Y', trim($row['date']))->format('Y-m-d') : null;
    //         $description = trim($row['description'] ?? '');

    //         if (empty($name) || empty($date) || in_array($date, $seenDays) || in_array($date, $existingDays)) {
    //             continue;
    //         }

    //         $seenDays[] = $date;
    //         $lastNumber++;
    //         $newhId = 'holi_' . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);

    //         $HolidaysData[] = [
    //             'h_id' => $newhId,
    //             'name' => $name,
    //             'date' => $date,
    //             'description' => $description,
    //         ];
    //     }

    //     if (!empty($HolidaysData)) {
    //         Holiday::insert($HolidaysData);
    //     }
    // }
    public function collection(Collection $collection)
{
    $lastHoliday = Holiday::orderBy('h_id', 'desc')->first();
    $lastNumber = $lastHoliday ? intval(substr($lastHoliday->h_id, 5)) : 0; // substr from 5 because 'holi_' is 5 chars

    $HolidaysData = [];
    $existingDays = Holiday::pluck('date')->map(fn($date) => strtolower(trim($date)))->toArray();
    $seenDays = [];

    foreach ($collection as $row) {
        $name = trim($row['name'] ?? '');
        $description = trim($row['description'] ?? '');
        $rawDate = trim($row['date'] ?? '');

        if (empty($name) || empty($rawDate)) {
            continue;
        }

        // Parse date safely
        try {
            // Try strict format d-m-Y first
            $date = \Carbon\Carbon::createFromFormat('d-m-Y', $rawDate)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                // Fallback to generic parse (e.g. Excel date, Y-m-d, etc)
                $date = \Carbon\Carbon::parse($rawDate)->format('Y-m-d');
            } catch (\Exception $e) {
                // Skip this row if no valid date
                continue;
            }
        }

        if (in_array($date, $seenDays) || in_array($date, $existingDays)) {
            continue;
        }

        $seenDays[] = $date;
        $lastNumber++;
        $newhId = 'holi_' . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);

        $HolidaysData[] = [
            'h_id' => $newhId,
            'name' => $name,
            'date' => $date,
            'description' => $description,
        ];
    }

    if (!empty($HolidaysData)) {
        Holiday::insert($HolidaysData);
    }
}

}
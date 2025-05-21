<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class EmployeeImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public Collection $rows;

    public function __construct()
    {
        $this->rows = collect();
    }

    public function collection(Collection $collection)
    {
        $collection->shift(); // remove header if needed

        foreach ($collection as $row) {
            $this->rows->push([
                'emp_id' => $row[0],
                'name' => $row[1],
                'email' => $row[2],
                // 'department' => $row[3] ?? null,
            ]);
        }
    }
}

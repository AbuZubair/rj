<?php

namespace App\Exports;

use App\Staff;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StaffMultiSheetExport implements WithMultipleSheets
{
    private $year;
    public function __construct()
    {
        $this->year = date('Y');
    }

    public function sheets(): array
    {
        $sheets = [];

        for ($i = 0; $i < 1; $i++) {
            $sheets[] = new StaffExport($i);
        }

        return $sheets;
    }
}
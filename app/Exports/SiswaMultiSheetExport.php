<?php

namespace App\Exports;

use App\Siswa;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SiswaMultiSheetExport implements WithMultipleSheets
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
            $sheets[] = new SiswaExport($i);
        }

        return $sheets;
    }
}
<?php

namespace App\Exports;

use App\ItemProduk;
use DateTime;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ProdukExport implements
    ShouldAutoSize,
    WithMapping,
    WithHeadings,
    FromQuery
{
    use Exportable;

    private $year;


    public function __construct(int $year)
    {
        $this->year = $year;

    }

    public function query()
    {
        return  ItemProduk::select('item_code','item_name','konversi','satuan_beli','satuan_jual','harga_beli','harga_jual')
            ->where('is_active','Y');
    }

    public function map($item): array
    {
        return [
            $item->item_code,
            $item->item_name,
            $item->satuan_beli,
            $item->satuan_jual,
            $item->konversi,
            number_format( $item->harga_beli , 2 , ',' , '.' ),
            number_format( $item->harga_jual , 2 , ',' , '.' ),
            $item->created_date
        ];
    }

    public function headings(): array
    {
        return [
            "item_code", "item_name", "satuan_beli", "satuan_jual", "konversi","harga_beli", "harga_jual"
        ];
    }
}
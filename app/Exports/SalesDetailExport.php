<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesDetailExport implements
WithMapping,
WithHeadings,
FromQuery
{

    private $year;
    private $month;
    private $date;

    public function __construct($year, $month, $date)
    {
        $this->year = $year;
        $this->month = $month;
        $this->date = $date;
    }

    public function query()
    {
        $query = DB::table('sales_detail')
            ->leftJoin('sales', 'sales.sales_no', '=', 'sales_detail.sales_no')
            ->leftJoin('item', 'item.item_code', '=', 'sales_detail.item_code')
            ->select(DB::raw('sales_detail.*,item.item_name,DATE_FORMAT(sales_date, "%d-%m-%Y") as datesales'));
        if( $this->year!='')$query->whereRaw("YEAR(sales_date) ='". $this->year."' ");
        if($this->month!='')$query->whereRaw("MONTH(sales_date) ='".$this->month."' ");
        if($this->date!='')$query->where("sales_date", $this->date);
        $query->orderBy('sales_date', 'desc');
        return $query;
    }

    public function map($item): array
    {
        return [
            $item->sales_no,
            $item->datesales,
            $item->item_code,
            $item->item_name,
            $item->quantity,
            $item->satuan,
            number_format( $item->harga , 2 , ',' , '.' ),
            number_format( $item->total_amount , 2 , ',' , '.' ),
            number_format( $item->discount_amount , 2 , ',' , '.' )
        ];
    }

    public function headings(): array
    {
        return [
            "Sales No", "Date", "Kode", "Item", "Quantity", "Satuan", "Harga Satuan", "Total", "Diskon"
        ];
    }
}
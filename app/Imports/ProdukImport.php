<?php

namespace App\Imports;

use App\ItemProduk;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class ProdukImport implements
    ToModel,
    WithHeadingRow,
    SkipsOnError,
    WithValidation,
    SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    public function model(array $row)
    {
        if(DB::table('parameter')->where('param','item_satuan')->where('value',strtolower(str_replace(' ', '_', $row['satuan_beli'])))->doesntExist()){
            DB::table('parameter')->insert([
                'param' => 'item_satuan',
                'value' => strtolower(str_replace(' ', '_', $row['satuan_beli'])),
                'label' => strtolower(str_replace(' ', '_', $row['satuan_beli']))
            ]);
        }
        if(DB::table('parameter')->where('param','item_satuan')->where('value',strtolower(str_replace(' ', '_', $row['satuan_jual'])))->doesntExist()){
            DB::table('parameter')->insert([
                'param' => 'item_satuan',
                'value' => strtolower(str_replace(' ', '_', $row['satuan_jual'])),
                'label' => strtolower(str_replace(' ', '_', $row['satuan_jual']))
            ]);
        }
        return new ItemProduk([
            'item_code'    => $row['item_code'],
            'item_name'    => $row['item_name'],
            'satuan_beli'  => strtolower(str_replace(' ', '_', $row['satuan_beli'])),
            'satuan_jual'  => strtolower(str_replace(' ', '_', $row['satuan_jual'])),
            'konversi'  => $row['konversi'],
            'harga_beli'  => $row['harga_beli'],
            'harga_jual'  => $row['harga_jual']
        ]);
    }

    public function rules(): array
    {
        return [
            '*.item_code' => [ 'unique:item,item_code']
        ];
    }

    public static function afterImport(AfterImport $event)
    {
    }

}
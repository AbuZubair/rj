<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Stock;
use App\ItemProduk;
use App\Library\Services\Shared;
use Illuminate\Support\Facades\Log;

class Sales extends Model
{
    protected $table = "sales";

    public $primaryKey = "id";

    const CREATED_AT = 'created_date';

    const UPDATED_AT = 'updated_date';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [    
        'id',
        'sales_no',
        'sales_code',
        'sales_date',
        'charge_amount',
        'reference_no',
        'status',
        'payment_type',
        'ba_id',
        'note',
        'created_date',
        'created_by',
        'updated_date',
        'updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_date', 'updated_date'
    ];

    public static function saveDetail($data,$sales_code)
    {
        $arrayData = array();
        $items = array();
        for ($i=0; $i < count($data['item']); $i++) { 
            $amount = $data['qty'][$i] * (int)str_replace(".","",$data['harga'][$i]);
            $disc = ($data['disc'][$i])?(int)str_replace(".","",$data['disc'][$i]):0;
            $item = array(
                'sales_no' => $data['sales_no'],
                'item_code' => substr(explode('code:',$data['item'][$i])[1], 0, -1),
                'quantity' => $data['qty'][$i],
                'satuan' => $data['satuan'][$i],
                'harga' => (int)str_replace(".","",$data['harga'][$i]),
                'discount_amount' => $disc,
                'total_amount' => ($disc!=0)?$amount - $disc:$amount
            );
            array_push($arrayData,$item);
            try {
                if(in_array($sales_code,['700'])){
                    $type = 'remove';
                    $item['transaction_code'] = $sales_code;
                    $item['transaction_no'] = $item['sales_no'];                    
                    Stock::updateStock($item,$type);
                    $produk = ItemProduk::where('item_code',$item['item_code'])->first();
                    array_push($items,[
                        'name' => $produk->item_name,
                        'qty' => $item['quantity'],
                        'price' => $item['total_amount'],
                    ]);
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        
        try {
            DB::table("sales_detail")->insert($arrayData);

            $msg = Auth::user()->getUsername(). 'insert sales detail succesfully : '.json_encode($data);              
            Log::info($msg);
            $shared = new Shared;
            $shared->logs($msg);
            return $items;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\ItemProduk;
use App\Stock;
use App\Library\Services\Shared;
use Illuminate\Support\Facades\Log;

class ItemTransaction extends Model
{
    protected $table = "item_transaction";

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
        'transaction_no',
        'transaction_code',
        'trans_year',
        'trans_month',
        'trans_date',
        'charge_amount',
        'note',
        'reference_no',
        'status',
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

    public function getQuery()
    {
        return DB::table('item_transaction')
            ->leftJoin('item_transaction_detail', 'item_transaction.transaction_no', '=', 'item_transaction_detail.transaction_no')
            ->leftJoin('item', 'item.item_code', '=', 'item_transaction_detail.item_code')
            ->select('item_transaction.*', 'item_transaction_detail.*','item.item_name','item.konversi as konversi_master');
    }

    public static function getFullDataByTransNo($req){
        $class = new ItemTransaction;
        $query = $class->getQuery();
        if($req->get('isRemaining') != null)$query->whereRaw('item_transaction_detail.item_code NOT IN ( select item_code from item_transaction_detail where reference_no = "'.$req->get('transaction_no').'" )');
        return $query->where('item_transaction.transaction_no',$req->get('transaction_no'))->get()->toArray();
    }

    public static function saveDetail($data,$transaction_code)
    {
        $arrayData = array();
        for ($i=0; $i < count($data['item']); $i++) { 
            $item = array(
                'transaction_no' => $data['transaction_no'],
                'item_code' => substr(explode('code:',$data['item'][$i])[1], 0, -1),
                'quantity' => $data['qty'][$i],
                'satuan' => $data['satuan'][$i],
                'harga' => (int)str_replace(".","",$data['harga'][$i]),
                'amount' => $data['qty'][$i] * (int)str_replace(".","",$data['harga'][$i]),
                'konversi' => isset($data['konversi'][$i])?(int)$data['konversi'][$i]:null,
                'reference_no' => isset($data['reference'])?$data['reference']:'',
            );
            array_push($arrayData,$item);
            if(isset($data['harga_jual'][$i]))$item['harga_jual'] = (int)str_replace(".","",$data['harga_jual'][$i]);
            $class = new ItemTransaction;
            try {
                if((isset($data['is_master_update'][$i]) && $data['is_master_update'][$i] == 1) || $transaction_code == '002'){
                    $class->updateItem($item);
                }

                if(in_array($transaction_code,['002','003'])){
                    $type = ($transaction_code == '002')?'add':'remove';
                    $item['transaction_code'] = $transaction_code;
                    if(isset($data['transDate']))$item['transDate'] = $data['transDate'];
                    Stock::updateStock($item,$type);
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        try {
            if($data['id']!=''){
                ItemTransaction::deleteDetail(array($data['transaction_no']));
            }
            DB::table("item_transaction_detail")->insert($arrayData);

            $msg = Auth::user()->getUsername(). 'insert item transaction detail succesfully : '.json_encode($data);              
            Log::info($msg);
            $shared = new Shared;
            $shared->logs($msg);
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }

    public function updateItem($data)
    {
        
        $datas = ItemProduk::where('item_code',$data['item_code'])->first();
        if($data['satuan']!='')$datas->satuan_beli = $data['satuan'];
        if($data['harga']!='')$datas->harga_beli = $data['harga'];
        if($data['konversi']!='')$datas->konversi = $data['konversi'];
        if(isset($data['harga_jual']) && $data['harga_jual']!='')$datas->harga_jual = $data['harga_jual'];
        if($datas->save()){  
            $msg = Auth::user()->getUsername(). 'update produk succesfully : '.json_encode($datas);              
            Log::info($msg);
            $shared = new Shared;
            $shared->logs($msg);
        }
    }

    public static function deleteDetail($data)
    {
        try {
            DB::table("item_transaction_detail")->whereIn('transaction_no', $data)->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getRefComplete($ref)
    {
        $query = DB::table("item_transaction_detail")->select(DB::raw('DISTINCT item_code'))
        ->whereRaw('transaction_no = "'.$ref.'" AND item_code NOT IN ( SELECT DISTINCT item_code FROM item_transaction_detail where reference_no = "'.$ref.'" )');
        $data = $query->count();
        return $data;
    }

}

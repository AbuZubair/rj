<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\ItemProduk;
use App\Library\Services\Shared;

class Stock extends Model
{
    protected $table = "stock_item";

    public $primaryKey = "id";

    const UPDATED_AT = 'updated_date';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [    
        'id',
        'item_code',
        'balance',
        'satuan',
        'updated_date',
        'updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public static function getStock($code=''){
        $data =  Stock::leftJoin('item', 'item.item_code', '=', 'stock_item.item_code')
            ->select('stock_item.*','item.item_name');
        if($code!='')$data->where('stock_item.item_code',$code);
        return $data;
    }

    public static function getStockCard(){
        $data =  DB::table('stock_card')->leftJoin('item', 'item.item_code', '=', 'stock_card.item_code')
            ->select('stock_card.*','item.item_name')->orderByDesc('created_date');
        return $data;
    }

    public static function updateStock($data,$type)
    {
        
        $item = ItemProduk::where('item_code',$data['item_code'])->first();
        $datas = Stock::where('item_code',$data['item_code'])->first();
        $qty = (isset($data['konversi']))?$data['quantity'] * $data['konversi']:$data['quantity'];
        if(isset($datas)){
            $add = $datas;
            $data['balanceBefore'] = $add->balance;
            if($type=='add'){
                $add->balance = $add->balance+$qty;
            }else{
                if($add->balance-$qty < 0){
                    throw new \Exception("Stock tidak mencukupi");
                }else{
                    $add->balance = $add->balance-$qty;
                }
            }
            $add->updated_by = Auth::user()->getUsername();
        }else{
            $data['balanceBefore'] = 0;
            $add = new Stock;
            $add->item_code = $data['item_code'];
            $add->balance = $qty;
            $add->satuan = $item->satuan_jual;
            $add->updated_by = Auth::user()->getUsername();
        }
        if($add->save()){  
            $msg = Auth::user()->getUsername(). 'update stock succesfully : '.json_encode($add);              
            Log::info($msg);
            $shared = new Shared;
            $shared->logs($msg);
            $data['qty'] = $qty;
            $data['balance'] = $add->balance;
            $data['satuan'] = $add->satuan;
            Stock::saveStockCard($data,$type);
        }
    }

    public static function saveStockCard($data,$type=null){
        try {
            $insert = array(
                'item_code' => $data['item_code'],
                'transaction_date' => isset($data['transDate'])?$data['transDate']:date("Y-m-d"),
                'transaction_code' => $data['transaction_code'],
                'transaction_no' => $data['transaction_no'],
                'stock_in' => ($type!=null && $type == 'add')?$data['qty']:0,
                'stock_out' => ($type!=null && $type == 'remove')?$data['qty']:0,
                'stock_before' => $data['balanceBefore'],
                'stock_balance' => $data['balance'],
                'satuan' => $data['satuan'],
                'created_by' => Auth::user()->getUsername()
            );
    
            DB::table("stock_card")->insert($insert);
    
            $msg = Auth::user()->getUsername(). 'insert stock card succesfully : '.json_encode($insert);              
            Log::info($msg);
            $shared = new Shared;
            $shared->logs($msg);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

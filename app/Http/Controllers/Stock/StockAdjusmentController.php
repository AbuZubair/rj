<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Stock;
use App\Http\Requests\Stock\StockAdjusmentRequest;

use Illuminate\Http\Request;

class StockAdjusmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $sharedService;
    private $model;

    public function __construct(Shared $sharedService,Model $model)
    {
        $this->sharedService = $sharedService;
        $this->model = $model;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        return view('stock.stock_adjusment.index');
    }

    public function crud(StockAdjusmentRequest $request, $req)
    {
        DB::beginTransaction();
        try{
            if($request->input('id')!=''){
                $data = Stock::find($request->input('id'));
            }else{
                if(Stock::where('item_code',$request->input('item_code'))->doesntExist()){
                    $data = new Stock;
                    $data->item_code = $request->input('item_code');
                    $data->satuan = $request->input('satuan');
                    $data->updated_by = Auth::user()->getUsername();
                }else{
                    echo json_encode(array('status' => 400, 'message' => 'Bad request'));
                    die();
                }
            }
            
            $balance_before = $data->balance;
            $data->balance = $request->input('adjust');
            if($data->save()){  
                $msg = Auth::user()->getUsername().' save stock adjustment succesfully : '.json_encode($data);              
                Log::info($msg);
                $this->sharedService->logs($msg);
                try {
                    $itemsc = array(
                        'transaction_no' => 'ADJ'.date('Ym').substr((string)time(), -4),
                        'transaction_code' => '999',
                        'item_code' => $request->input('item_code'),
                        'balance' => $request->input('adjust'),
                        'satuan' => $request->input('satuan'),
                        'transDate' => date("Y-m-d"),
                        'balanceBefore' => $balance_before,
                    );
                    Stock::saveStockCard($itemsc);
                    echo json_encode(array('status' => 200, 'message' => 'Process Succesfully'));
                    DB::commit();
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        }
        catch (Exception $e){   
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully stock adjustment '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => 'Proccess Unsuccessfully'));
            DB::rollBack();
        }
 
    }

    public function delete(Request $request)
    {
        try {      
            $id = $request->post('data');
            $data = ItemTransaction::whereIn('id', $id);      
            $detail = $data->select('transaction_no')->get()->toArray();
            ItemTransaction::deleteDetail($detail);
            $msg = Auth::user()->getUsername(). ' delete item produk succesfully : '.json_encode($data->get()->toArray());              
            Log::info($msg);
            $this->sharedService->logs($msg);
            $data->delete();
            echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan'));
        }catch (Exception $e){
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully delete item produk: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => $e->getMessage()));
        }
        
    }

    public function getStok(Request $request)
    {
        $data = Stock::getStock($request->get('item_code'))->first();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }


}

<?php

namespace App\Http\Controllers\ItemTransaction;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\ItemTransaction;
use App\Http\Requests\ItemTransaction\PORequest;

use Illuminate\Http\Request;

class POController extends Controller
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
        return view('item_transaction.po.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data =  ItemTransaction::latest()->where('transaction_code','001');
            if($request->get('transaction_no')!='')$data->where('transaction_no',$request->get('transaction_no'));
            if($request->get('date')!=''){
                $data->where('trans_year',date_format(date_create($request->input('date')),"Y"))
                ->where('trans_month',date_format(date_create($request->input('date')),"m"))
                ->where('trans_date',date_format(date_create($request->input('date')),"d"));
            }
            if($request->get('status')!='')$data->where('status',$request->get('status'));
            return Datatables::of($data)               
                ->make(true);
        }
    }

    public function add()
    {
        $type = 'Add';
        return view('inventory.po.form',compact('type'));
    }

    public function edit(Request $req)
    {
        $data = ItemTransaction::getFullDataByTransNo($req);
        echo json_encode(array('status' => true, 'message' => 'Process Succesfully', 'data' => $data));
    }

    public function crud(PORequest $request, $req)
    {
        DB::beginTransaction();
        try{
            $data = new ItemTransaction;
            if($request->input('id') != ''){
                $data = ItemTransaction::find($request->input('id'));
                $data->updated_by = Auth::user()->getUsername();
            }else{
                $data->created_by = Auth::user()->getUsername();
                $data->transaction_code = '001';
            }
            $data->transaction_no = $request->input('transaction_no');
            $data->trans_year = date('Y');
            $data->trans_month = date('m');
            $data->trans_date = date('d');
            $data->charge_amount = (int)str_replace(".","",$request->input('charge_amount'));
            $data->note = $request->input('note');
            $data->status = 0;
            if($data->save()){  
                $detail = ItemTransaction::saveDetail($request->input(),$data->transaction_code);
                if($detail){
                    $type = ($request->input('id') == '')?' save':' update';
                    $msg = Auth::user()->getUsername(). $type.' item transaction succesfully : '.json_encode($data);              
                    Log::info($msg);
                    $this->sharedService->logs($msg);
                    echo json_encode(array('status' => 200, 'message' => 'Process Succesfully'));
                    DB::commit();
                }
            }
        }
        catch (Exception $e){   
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully'.($request->input('id') == '')?' save':' update'. ' item transaction: '.$e->getMessage());       
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

    public function getDropdown(Request $request)
    {
        $query =  ItemTransaction::where('transaction_code','001');
        if($request->get('status') != null)$query->whereIn('status',$request->get('status'));
        $data = $query->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function getParams(Request $request)
    {
        $data =  new \stdClass();
        $data->item_satuan = $this->sharedService->getParamDropdown('item_satuan');
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

}

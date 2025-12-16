<?php

namespace App\Http\Controllers;

use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\DB;
use App\Transaction;
use App\Murabahah;
use App\Http\Requests\TransRequest;

class TransController extends Controller
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

    public function index(Request $request)
    {
        return view('trans.index');
    }

    public function getData(Request $request)
    {       
            $data = Transaction::getData($request);
            return Datatables::of($data)               
                ->make(true);
    }

    public function edit(Request $req)
    {
        $id = $req->get('id');
        $data = Transaction::where('id', $id)
            ->select(DB::raw('transaction.id,transaction.coa_code,transaction.amount,transaction.tans_desc,transaction.trans_date,transaction.trans_month,transaction.trans_year, transaction.dk'))
            ->get()->first();
        echo json_encode(array('status' => 200, 'message' => 'Process Succesfully', 'data' => $data));
    }

    public function crud(TransRequest $request)
    {        
        DB::beginTransaction();
        try{
            $isEdit = ($request->input('id') != '')? true : false;
            $data = new Transaction;
            $amt = str_replace(".","",$request->input('amount'));
            $amount =  str_replace(",",".",$amt);
            $dk = $request->input('dk');
            $coa_code = $request->input('coa_code');
            if($isEdit){
                $data = Transaction::find($request->input('id'));
                $temp_amt = $data->amount;
                $data->updated_by = Auth::user()->getUsername();
            }else{
                $coa_code = $request->input('coa_code_debit');
                if(isset($coa_code) && $coa_code != ''){
                    $dk = 'debit';
                }else{
                    $dk = 'kredit';
                    $coa_code = $request->input('coa_code_kredit');
                }
            }
            $data->trans_year = date_format(date_create($request->input('transDate')),"Y");
            $data->trans_month = date_format(date_create($request->input('transDate')),"m");
            $data->trans_date = date_format(date_create($request->input('transDate')),"d");
            $data->amount = $amount;
            $data->dk = $dk;
            $data->coa_code = $coa_code;
            $data->tans_desc = $request->input('tans_desc'); 
            if($request->input('trans_type')!=null)$data->trans_type = $request->input('trans_type');
            if($data->save()){  
                $type = ($request->input('id') == '')?' save':' update';
                $msg = Auth::user()->getUsername(). $type.' transaction succesfully : '.json_encode($data);              
                Log::info($msg);
                $this->sharedService->logs($msg);

                if(!$isEdit){
                    $input_coa_code_debit = $request->input('coa_code_debit');
                    $input_coa_code_kredit = $request->input('coa_code_kredit');
                    if((isset($input_coa_code_debit) && $input_coa_code_debit != '') && (isset($input_coa_code_kredit) && $input_coa_code_kredit != '')){
                        $dk_pair = null;
                        $coa_code_pair = null;
                        if($dk === 'debit'){
                            $dk_pair = 'kredit';
                            $coa_code_pair = $input_coa_code_kredit;
                        }else{
                            $dk_pair = 'debit';
                            $coa_code_pair = $input_coa_code_debit;
                        }
                        // Create pair input
                        $this->createPairTrans(
                            $request->input('transDate'),
                            $data->amount,
                            $coa_code_pair,
                            $dk_pair,
                            $data->tans_desc
                        );
                    }
                }
                /*************************** */
                
                echo json_encode(array('status' => 200, 'message' => 'Process Succesfully'));
                DB::commit();
            }
        }
        catch (Exception $e){   
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully'.($request->input('id') == '')?' save':' update'. ' transaction: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => 'Proccess Unsuccessfully'));
            DB::rollBack();
        }
            
    }

    public function delete(Request $request)
    {
        try {      
            $id = $request->post('data');
            $data = Transaction::whereIn('id', $id);            
            $msg = Auth::user()->getUsername(). ' delete transaction succesfully : '.json_encode($data->get()->toArray());              
            Log::info($msg);
            $this->sharedService->logs($msg);
            $data->delete();
            echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan'));
        }catch (Exception $e){
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully delete transaction: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => $e->getMessage()));
        }
        
    }

    public function getDropdown(Request $request)
    {
        $st = [];
        if($request->get('status')!='')array_push($st,$request->get('status'));
        $status = $st;
        $data = $this->sharedService->getDropdown($status);
        echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function createPairTrans($date,$amount,$coa,$dk, $desc)
    {
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));
        $day = date('d', strtotime($date));
        $arr = [
            [
                'trans_year' => $year, 
                'trans_month' => $month, 
                'trans_date' => $day, 
                'amount' => (int)str_replace(".","",$amount), 
                'dk' => $dk, 
                'coa_code' => $coa, 
                'tans_desc' => $desc, 
                'created_by' => Auth::user()->getUsername()
            ]
        ];
        DB::table('transaction')->insert($arr);
    }

}

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
            ->select(DB::raw('transaction.id,transaction.dk,transaction.coa_code,transaction.amount,transaction.no_anggota,
                transaction.no_murabahah,transaction.tans_desc,transaction.trans_date,transaction.trans_month,transaction.trans_year,
                CASE WHEN (transaction.trans_type = "sembako") THEN "other" ELSE transaction.trans_type END as trans_type'))
            ->get()->first();
        echo json_encode(array('status' => 200, 'message' => 'Process Succesfully', 'data' => $data));
    }

    public function crud(TransRequest $request, $req)
    {        
        DB::beginTransaction();
        try{
            $data = new Transaction;
            $amt = str_replace(".","",$request->input('amount'));
            $amount =  str_replace(",",".",$amt);
            $dk = null;
            $coa_code = null;
            if($request->input('id') != ''){
                $data = Transaction::find($request->input('id'));
                if($data->trans_type == 'murabahah'){
                    $murabahah = Murabahah::where('no_murabahah',$data->no_murabahah)->first();
                    $pembayaran = ($murabahah->nilai_pembayaran - $data->amount) +  $amount;                    
                }
                $temp_amt = $data->amount;
                $data->updated_by = Auth::user()->getUsername();
                $dk = $request->input('dk');
                $coa_code = $request->input('coa_code');
            }else{
                $data->created_by = Auth::user()->getUsername();
                if($request->input('trans_type') == 'murabahah'){
                    $murabahah = Murabahah::where('no_murabahah',$request->input('no_murabahah'))->first();
                    $pembayaran = $murabahah->nilai_pembayaran +  $amount;
                    if($murabahah->deduction == $murabahah->margin){
                        if($pembayaran < $murabahah->nilai_total){
                            echo json_encode(array('status' => 301, 'message' => 'Total yang harus dibayarkan '.$murabahah->nilai_total - $murabahah->nilai_pembayaran.', karena sudah masuk bulan terakhir'));
                            die();
                        }
                    }
                } else if($request->input('trans_type') == 'other'){
                    $dk = 'kredit';
                    $coa_code = $request->input('coa_code_kredit');
                }
            }
            $data->trans_year = date_format(date_create($request->input('transDate')),"Y");
            $data->trans_month = date_format(date_create($request->input('transDate')),"m");
            $data->trans_date = date_format(date_create($request->input('transDate')),"d");
            if($request->input('no_murabahah')!=null)$data->no_murabahah = $request->input('no_murabahah');
            $data->no_anggota = $request->input('no_anggota');
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

                /*Update murabahah */
                if($request->input('id') == ''){
                    if($request->input('trans_type') == 'murabahah' || $data->trans_type == 'murabahah'){
                        $murabahah = Murabahah::where('no_murabahah',$request->input('no_murabahah'))->first();
                        $murabahah->nilai_pembayaran = $pembayaran;
                        $murabahah->status = ($pembayaran == $murabahah->nilai_total)?2:1;
                        $murabahah->deduction = $murabahah->deduction + 1;
                        $murabahah->save();
                        $this->updateBankAkun($request->input('transDate'),$request->input('amount'),'A.1.1.1','debit', 'Bank - Piutang Kredit no: '.$murabahah->no_murabahah.' ');
                    } else if($request->input('trans_type') == 'other' || $data->trans_type == 'other'){
                        // Create debit input
                        $this->updateBankAkun(
                            $request->input('transDate'),
                            $data->amount,
                            $request->input('coa_code_debit'),
                            'debit',
                            $data->tans_desc
                        );
                    }
                }else{
                    if($request->input('trans_type') == 'murabahah' || $data->trans_type == 'murabahah'){
                        $murabahah = Murabahah::where('no_murabahah',$data->no_murabahah)->first();
                        $murabahah->nilai_pembayaran = $pembayaran;
                        $murabahah->status = ($pembayaran == $murabahah->nilai_total)?2:1;
                        $murabahah->save();
                        $date=date_create($data->trans_year."-". $data->trans_month."-".$data->trans_date);
                        $dk = ($amount < $temp_amt)?'kredit':'debit';
                        $amt = abs($temp_amt - $amount);
                        $this->updateBankAkun(date_format($date,"Y-m-d"),$amt,'A.1.1.1',$dk, 'Bank - Edit Piutang Kredit no: '.$murabahah->no_murabahah.' ');
                    }  
                }
                                
                if($request->input('trans_type') == 'konsinasi' || $data->trans_type == 'konsinasi'){
                    $this->updateBankAkun($request->input('transDate'),$request->input('amount'),'A.1.0','debit', 'Kas - '. $data->tans_desc, 'konsinasi');
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

    public function updateBankAkun($date,$amount,$coa,$dk, $desc, $type = null)
    {
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));
        $day = date('d', strtotime($date));
        $arr = [
            ['trans_year' => $year, 'trans_month' => $month, 'trans_date' => $day, 'amount' => (int)str_replace(".","",$amount), 'trans_type' => $type!=null?$type:'other', 'dk' => $dk, 'coa_code' => $coa, 'tans_desc' => $desc, 'created_by' => Auth::user()->getUsername()]
        ];
        DB::table('transaction')->insert($arr);
    }

}

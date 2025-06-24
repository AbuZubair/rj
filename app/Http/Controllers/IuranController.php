<?php

namespace App\Http\Controllers;

use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Hash;
use App\Iuran;
use App\Anggota;
use App\Http\Requests\IuranRequest;

class IuranController extends Controller
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
        return view('iuran.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Iuran::getData($request);
            return Datatables::of($data)               
                ->make(true);
        }
    }

    public function add()
    {
        $type = 'Add';
        return view('user.form',compact('type'));
    }

    public function edit(Request $req)
    {
        $id = $req->get('id');
        $data = Iuran::find($id)->toArray();
        echo json_encode(array('status' => 200, 'message' => 'Process Succesfully', 'data' => $data));
    }

    public function crud(IuranRequest $request, $req)
    {

        try{
            $data = new Iuran;
            if($request->input('id') != ''){
                $data = Iuran::find($request->input('id'));
                $data->updated_by = Auth::user()->getUsername();
            }else{
                $data->created_by = Auth::user()->getUsername();
            }
            $data->no_anggota = $request->input('no_anggota');
            $data->month = $request->input('month');
            $data->year = $request->input('year');
            $val = str_replace(".","",$request->input('amount'));
            $data->amount = (int)$val;   
            $data->reference = $request->input('reference');
            $data->status = 1;
            $data->type = $request->input('type');
            if($data->save()){  
                $type = ($request->input('id') == '')?' save':' update';
                $msg = Auth::user()->getUsername(). $type.' iuran succesfully : '.json_encode($data);              
                Log::info($msg);
                $this->sharedService->logs($msg);
                if($data->type == 2 && $data->reference == 4){
                    /* Update thr monthly */
                    $anggota = Anggota::where('no_anggota',$data->no_anggota)->first();
                    $anggota->is_thr_monthly = "N";
                    $anggota->thr = 0;
                    $anggota->save();
                    /******************** */
                }
                $this->addTransaction($data);
                echo json_encode(array('status' => 200, 'message' => 'Process Succesfully'));
            }
        }
        catch (Exception $e){   
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully'.($request->input('id') == '')?' save':' update'. ' Iuran: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => 'Proccess Unsuccessfully'));
        }
            
    }

    public function addTransaction($data)
    {
        $year = $data->year;
        $month = $data->month;
        $day = date('d');
        $anggota = Anggota::where('no_anggota',$data->no_anggota)->first();
        $dk = ($data->type == 2)?'debit':'kredit';
        $dk_bank = ($dk=='kredit')?'debit':'kredit';
        switch ($data->type) {
            case 0:
                $desc = 'Simpanan Wajib a/n '.$anggota->fullname;
                $coa = 'C.2';
                break;
            case 1:
                $desc = 'Simpanan Pokok a/n '.$anggota->fullname;
                $coa = 'C.1';
                break;
            case 2:
                if($data->reference==0){
                    $desc = '(Return) Simpanan Wajib a/n '.$anggota->fullname;
                    $coa = 'C.2';
                }else if($data->reference==1){
                    $desc = '(Return) Simpanan Pokok a/n '.$anggota->fullname;
                    $coa = 'C.1';
                }else{
                    $desc = '(Return) Tabungan Idul Fitri a/n '.$anggota->fullname;
                    $coa = 'B.1.2.1';
                }
                
                break;
            case 4:
                $desc = 'Tabungan Idul Fitri a/n '.$anggota->fullname;
                $coa = 'B.1.2.1';
                break;
            default:
                # code...
                break;
        }
        $arr = [
            ['trans_year' => $year, 'trans_month' => $month, 'trans_date' => $day, 'amount' => $data->amount, 'trans_type' => 'other', 'dk' => $dk, 'coa_code' => $coa, 'tans_desc' => $desc, 'created_by' => Auth::user()->getUsername()],
            ['trans_year' => $year, 'trans_month' => $month, 'trans_date' => $day, 'amount' => $data->amount, 'trans_type' => 'other', 'dk' => $dk_bank, 'coa_code' => 'A.1.1.1', 'tans_desc' => 'Bank - '.$desc, 'created_by' => Auth::user()->getUsername()],
        ];
        DB::table('transaction')->insert($arr);
    }

    public function delete(Request $request)
    {
        try {      
            $id = $request->post('data');
            $data = Iuran::whereIn('id', $id);            
            $msg = Auth::user()->getUsername(). ' delete Iuran succesfully : '.json_encode($data->get()->toArray());              
            Log::info($msg);
            $this->sharedService->logs($msg);
            $data->delete();
            echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan'));
        }catch (Exception $e){
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully delete Iuran: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => $e->getMessage()));
        }
        
    }

    public function UpdateStatus(Request $request)
    {
        try {      
            $id = $request->post('data');
            if($id!=''){
                $data = Iuran::whereIn('id', $id);
                $arr = array(
                    'status' => $request->post('status'),
                    'updated_by' => Auth::user()->getUsername()
                );
                if($request->post('status') == '1'){
                    $arr['paid_date'] = date('Y-m-d');
                }
                $data->update($arr);
            }else{
                $month = $this->sharedService->getMonthName(date("m"));
                $data = Iuran::where(['month' => $month, 'year' => date("Y")]);
                $data->update(['status' => $request->post('status'), 'updated_by' => Auth::user()->getUsername(), 'paid_date' => date('Y-m-d')]);
            }         
            
            $msg = Auth::user()->getUsername(). ' update status Iuran succesfully : '.json_encode($data->get()->toArray());              
            Log::info($msg);
            $this->sharedService->logs($msg);
            echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan'));
        }catch (Exception $e){
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully update status Iuran: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => $e->getMessage()));
        }
    }

    public function getParams(Request $request)
    {
        $data =  new \stdClass();
        $data->type = $this->sharedService->getParamDropdown('type_iuran');
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function getDropdownList(Request $request)
    {
        $data = Iuran::getList($request);
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function SetThr(Request $request)
    {
        $data = $request->post('data');
        Anggota::where('no_anggota', Auth::user()->getNoAnggota())
            ->update(['thr' =>  (int)str_replace(".","",$data['thr']), 'is_thr_monthly' => $data['is_thr_monthly'], 'thr_date' => date('Y-m-d')]);

        $msg = Auth::user()->getUsername(). ' set thr succesfully : '.json_encode($data);              
        Log::info($msg);
        $this->sharedService->logs($msg);

        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan'));
    }

    public function getThr(Request $request)
    {
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => Anggota::where('no_anggota', Auth::user()->getNoAnggota())->first()));
    }

    public function getAmount(Request $request)
    {
        $total = Iuran::where('no_anggota',$request->get('no_anggota'))->where('type',$request->get('type'))->sum('amount');
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $total));
    }
}

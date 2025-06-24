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
use App\Murabahah;
use App\Pengajuan;
use App\Anggota;
use App\Http\Requests\PengajuanRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class PengajuanController extends Controller
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
        return view('pengajuan.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Pengajuan::getData($request);
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
        $data = Pengajuan::find($id)->toArray();
        echo json_encode(array('status' => 200, 'message' => 'Process Succesfully', 'data' => $data));
    }

    public function crud(PengajuanRequest $request, $req)
    {
        DB::beginTransaction();
        try{
            $data = new Pengajuan;
            $nilai_awal = (int)str_replace(".","",$request->input('nilai_awal'));
            $type_murabahah = $request->input('type');
            $is_new = false;
            $anggota = null;
                        
            if($request->input('id') != ''){
                $data = Pengajuan::find($request->input('id'));
                $anggota = Anggota::where('no_anggota', $data->no_anggota)->first();
                $nilai_awal_before = $data->nilai_awal;
                $data->updated_by = Auth::user()->getUsername();
                $type_murabahah = $data->type;
            }else{
                $is_new = true;
                $anggota = Anggota::where('no_anggota', $request->input('no_anggota'))->first();
                $data->created_by = Auth::user()->getUsername();
                $data->no_anggota = $request->input('no_anggota');
                $data->type = $type_murabahah;
                $cek_limit = Murabahah::where('no_anggota',$data->no_anggota)->where('status','!=',2)->sum('nilai_awal');
                if (($anggota->limit_kredit < ($nilai_awal+$cek_limit)) && ($type_murabahah == 0)) {
                    echo json_encode(array('status' => 500, 'message' => 'Anggota mencapai limit kredit'));
                    die();
                }
            }
            $data->date = date('Y-m-d');
            $data->margin = $request->input('margin');
            $nilai_total = ($type_murabahah == 0)?$nilai_awal + ($nilai_awal*(int)$request->input('margin') / 100):(int)str_replace(".","",$request->input('nilai_total_jasa'));
            $data->nilai_awal = $nilai_awal;
            $data->nilai_total = $nilai_total;
            $angsuran = $nilai_total / (int)$request->input('margin');
            $data->angsuran = number_format((float)$angsuran, 2, '.', '');   
            $data->desc = $request->input('desc');       
            if($data->save()){  
                $type = ($request->input('id') == '')?' save':' update';
                $msg = Auth::user()->getUsername(). $type.' pengajuan succesfully : '.json_encode($data);              
                Log::info($msg);
                $this->sharedService->logs($msg);

                if($is_new){
                    /**
                     * Send email notif
                     */
                    $data->fullname = $anggota->fullname;
                    $this->sharedService->sendEmail('kopkar@kpms.co.id',$data,'pengajuan');

                }               

                echo json_encode(array('status' => 200, 'message' => 'Process Succesfully'));
                DB::commit();
            }
        }
        catch (Exception $e){   
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully'.($request->input('id') == '')?' save':' update'. ' murabahah: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => 'Proccess Unsuccessfully'));
            DB::rollBack();
        }
            
    }

    public function delete(Request $request)
    {
        try {      
            $id = $request->post('data');
            $data = Pengajuan::whereIn('id', $id);            
            $msg = Auth::user()->getUsername(). ' delete pengajuan succesfully : '.json_encode($data->get()->toArray());              
            Log::info($msg);
            $this->sharedService->logs($msg);
            $data->delete();
            echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan'));
        }catch (Exception $e){
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully delete murabahah: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => $e->getMessage()));
        }
        
    }

    public function getLevelDropdown()
    {
        $max = Coa::max('coa_level');
        $array = array();
        for ($i=0; $i <= $max; $i++) {
            $array[$i] = $i+1;
        }
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $array));
    }

    public function getDropdownList()
    {
        $data = Murabahah::_getQuery()->where('status','!=', '2')->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function approval(Request $request)
    {
        $status = $request->post('code');
        $id = $request->post('id');
        $data = Pengajuan::find($id);
        $data->status = $status;
        if($request->post('nilai_total') != null)$data->nilai_total = (int)str_replace(".","",$request->post('nilai_total'));
        if($request->post('desc') != null)$data->desc = $request->post('desc');
        if($data->save()){  
            $msg = Auth::user()->getUsername(). ' save approval pengajuan succesfully : '.json_encode($data);              
            Log::info($msg);
            $this->sharedService->logs($msg);
            echo json_encode(array('status' => 200, 'message' => 'Process Succesfully'));
        }
    }

    public function download(Request $req) {
        $id = $req->get('id');
        $data = Pengajuan::_getQuery()->where('pengajuan.id',$id)->first();
        $pdf = Pdf::loadView('pengajuan_print', ['data' => $data]);
    
        return $pdf->download('Pengajuan '.$data->fullname. '_'.$data->date.'.pdf');
    }

}
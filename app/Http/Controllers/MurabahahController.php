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
use App\Anggota;
use App\Http\Requests\MurabahahRequest;

class MurabahahController extends Controller
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
        return view('murabahah.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Murabahah::getData($request);
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
        $data = Murabahah::select(DB::raw('murabahah.*,(case when type = 1 then nilai_total ELSE null END) as nilai_total_jasa '))->find($id)->toArray();
        echo json_encode(array('status' => 200, 'message' => 'Process Succesfully', 'data' => $data));
    }

    public function crud(MurabahahRequest $request, $req)
    {
        DB::beginTransaction();
        try{
            $data = new Murabahah;
            $nilai_awal = (int)str_replace(".","",$request->input('nilai_awal'));
            $type_murabahah = $request->input('type');
            $transport = ($request->input('nilai_transport') != '')?(int)str_replace(".","",$request->input('nilai_transport')):0;
            
            if($request->input('id') != ''){
                $data = Murabahah::find($request->input('id'));
                $anggota = Anggota::where('no_anggota', $data->no_anggota)->first();
                $nilai_awal_before = $data->nilai_awal;
                $data->updated_by = Auth::user()->getUsername();
                $type_murabahah = $data->type;
            }else{
                $anggota = Anggota::where('no_anggota', $request->input('no_anggota'))->first();
                $data->created_by = Auth::user()->getUsername();
                $data->no_anggota = $request->input('no_anggota');
                $data->type = $type_murabahah;
                $cek_limit = Murabahah::where('no_anggota',$data->no_anggota)->where('status','!=',2)->sum('nilai_awal');
                if (($anggota->limit_kredit < ($nilai_awal+$cek_limit)) && ($type_murabahah == 0)) {
                    echo json_encode(array('status' => 400, 'message' => 'Anggota mencapai limit kredit'));
                    die();
                }
            }
            if(Murabahah::where('no_murabahah',$request->input('no_murabahah'))->exists()){
                echo json_encode(array('status' => 400, 'message' => 'No. Akad exist, silahkan refresh atau close terlebih dahulu'));
                die();
            }
            $data->no_murabahah = $request->input('no_murabahah');
            $data->date = $request->input('date');
            $data->date_trans = $request->input('date_trans');
            $data->margin = $request->input('margin');
            $nilai_total = ($type_murabahah == 0)?$nilai_awal + ($nilai_awal*(int)$request->input('margin') / 100):(int)str_replace(".","",$request->input('nilai_total_jasa'));
            $data->nilai_awal = $nilai_awal;
            $data->nilai_total = $nilai_total + $transport;
            $data->nilai_transport = $transport;
            $angsuran = $nilai_total / (int)$request->input('margin');
            $data->angsuran = number_format((float)$angsuran, 2, '.', '');   
            $data->desc = $request->input('desc');       
            if($request->input('pengajuan_id') != null)$data->pengajuan_id = $request->input('pengajuan_id');
            if($data->save()){  
                $type = ($request->input('id') == '')?' save':' update';
                $msg = Auth::user()->getUsername(). $type.' murabahah succesfully : '.json_encode($data);              
                Log::info($msg);
                $this->sharedService->logs($msg);

                if($type_murabahah == 0){
                    /*Update limit kredit anggota */
                    // $anggota->limit_kredit = $limit;
                    // $anggota->save();
                    /*************************** */
                }
                
                $this->addTransaction($data,$anggota);

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

    public function addTransaction($data, $anggota)
    {
        $year = date('Y', strtotime($data->date_trans));
        $month = date('m', strtotime($data->date_trans));
        $day = date('d', strtotime($data->date_trans));
        $arr = [
            ['trans_year' => $year, 'trans_month' => $month, 'trans_date' => $day, 'no_murabahah' => $data->no_murabahah, 'amount' => $data->nilai_awal, 'trans_type' => 'murabahah', 'dk' => 'debit', 'coa_code' => 'D.1.2', 'tans_desc' => 'Beban Anggota a/n '.$anggota->fullname, 'created_by' => Auth::user()->getUsername()],
            ['trans_year' => $year, 'trans_month' => $month, 'trans_date' => $day, 'no_murabahah' => $data->no_murabahah, 'amount' => $data->nilai_awal, 'trans_type' => 'murabahah', 'dk' => 'kredit', 'coa_code' => 'A.1.1.1', 'tans_desc' => 'Bank - kredit a/n '.$anggota->fullname, 'created_by' => Auth::user()->getUsername()],
            ['trans_year' => $year, 'trans_month' => $month, 'trans_date' => $day, 'no_murabahah' => $data->no_murabahah, 'amount' => $data->nilai_total, 'trans_type' => 'murabahah', 'dk' => 'debit', 'coa_code' => 'A.1.2', 'tans_desc' => 'Piutang Usaha a/n '.$anggota->fullname, 'created_by' => Auth::user()->getUsername()],
            ['trans_year' => $year, 'trans_month' => $month, 'trans_date' => $day, 'no_murabahah' => $data->no_murabahah, 'amount' => $data->nilai_total - $data->nilai_transport, 'trans_type' => 'murabahah', 'dk' => 'kredit', 'coa_code' => 'D.1.1', 'tans_desc' => 'Partisipasi Bruto Anggota a/n '.$anggota->fullname, 'created_by' => Auth::user()->getUsername()]
        ];
        if($data->nilai_transport != 0)array_push($arr,['trans_year' => $year, 'trans_month' => $month, 'trans_date' => $day, 'no_murabahah' => $data->no_murabahah, 'amount' => $data->nilai_transport, 'trans_type' => 'murabahah', 'dk' => 'kredit', 'coa_code' => 'E.1.3', 'tans_desc' => 'Beban Transport a/n '.$anggota->fullname, 'created_by' => Auth::user()->getUsername()]);
        DB::table('transaction')->insert($arr);
    }

    public function delete(Request $request)
    {
        try {      
            $id = $request->post('data');
            $data = Murabahah::whereIn('id', $id);            
            $msg = Auth::user()->getUsername(). ' delete murabahah succesfully : '.json_encode($data->get()->toArray());              
            Log::info($msg);
            $this->sharedService->logs($msg);

             /*revert limit kredit */
            //  $arr = $data->get();
            //  for ($i=0; $i < count($arr); $i++) { 
            //      $anggota = Anggota::where('no_anggota', $arr[$i]->no_anggota)->first();
            //      $anggota->limit_kredit = $anggota->limit_kredit + $arr[$i]->nilai_awal;
            //      $anggota->save();
            //  }
             /******************** */

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

    public function getDropdownList(Request $request)
    {

        $query = Murabahah::_getQuery();
        if($request->get("status") == null || $request->get("status") == '')$query->where('status','!=', '2');
        if($request->get("no_anggota") != null)$query->where('murabahah.no_anggota',$request->get("no_anggota"));
        $data = $query->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function checkUpdate()
    {
        try {
            $result = Murabahah::checkAndUpdate();
            echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}

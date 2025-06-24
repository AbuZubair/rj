<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Pengurus;
use App\Anggota;

use Illuminate\Http\Request;

class PengurusController extends Controller
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
        return view('master.pengurus.index');
    }

    public function getData(Request $request)
    {
        $query = Pengurus::mainQuery();
        $data = $query
                ->where('parameter.param','kepengurusan')
                ->where('parameter.is_active','Y')
                ->orderBy('id')
                ->select(DB::raw('parameter.*,param.value as persentase, anggota.fullname, anggota.no_anggota'))
                ->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Proses berhasil dilakukan', 'data' => $data));
    }

    public function save(Request $request)
    {
        try {
            $data = $request->post();
            $keys = array_keys($data);
            /* Cek kalo ada kepengurusan yg di delete */
            $arr_value = array_filter($keys,function($v){
                if(!str_contains($v, 'persentase') && !str_contains($v, 'label'))return $v;
            });
            if(DB::table('parameter')->where('param','kepengurusan')->where('is_active','Y')->whereNotIn('value', $arr_value)->count() > 0){
                DB::table('parameter')->where('param','kepengurusan')->whereNotIn('value', $arr_value)->update(['is_active' => 'N']);
                Anggota::whereIn('pengurus',function ($query) {
                    $query->select('value')
                        ->from('parameter')
                        ->where('param','kepengurusan')
                        ->where('is_active','N');
                })->update(['is_pengurus' => 'N', 'pengurus' => NULL]);
            }        
            /***************************************** */
            for ($i=0; $i < count($keys); $i++) { 
                $key = $keys[$i];
                if(!str_contains($key, 'input')){
                    if (str_contains($key, 'persentase')) { 
                        DB::table('parameter')
                            ->where('param','persen_pengurus')
                            ->where('param1',str_replace("_persentase","",$key))
                            ->update(['value' => $data[$key], 'label' => $data[$key]]);
                    }else if (str_contains($key, 'label')) { 
                        DB::table('parameter')
                            ->where('param','kepengurusan')
                            ->where('value',str_replace("_label","",$key))
                            ->update(['label' => $data[$key]]);
                    }else{
                        if(Anggota::where('pengurus',$key)->where('no_anggota','!=',$data[$key])->count() > 0){
                            Anggota::where('pengurus',$key)->update(['is_pengurus' => 'N', 'pengurus' => NULL]);
                        }   
                        Anggota::where('no_anggota',$data[$key])->update(['is_pengurus' => 'Y', 'pengurus' => $key]);
                    }
                }
            }
            /* Input baru */
            if(in_array('input_pengurus',$keys)){
                $insert = [];
                foreach ($data['input_pengurus'] as $k => $value) {
                    $pengurus = strtolower(str_replace(' ', '_', $data['input_label'][$k]));
                    array_push($insert,array(
                        "value" => $pengurus,
                        "label" => $data['input_label'][$k],
                        "param" => "kepengurusan",
                        "param1" => NULL
                    ),array(
                        "value" => $data['input_persentase'][$k],
                        "label" => $data['input_persentase'][$k],
                        "param" => "persen_pengurus",
                        "param1" => $pengurus
                    ));
                    if(DB::table('parameter')->where('param','kepengurusan')->where('value',$pengurus)->count() > 0){
                        DB::table('parameter')->where('param','kepengurusan')->where('value',$pengurus)->delete();
                        DB::table('parameter')->where('param','persen_pengurus')->where('param1',$pengurus)->delete();
                    }
                    Anggota::where('no_anggota',$data['input_pengurus'][$k])->update(['is_pengurus' => 'Y', 'pengurus' => $pengurus]);
                }
                DB::table('parameter')->insert($insert);
            }
            /************ */
            $msg = Auth::user()->getUsername(). ' update pengurus succesfully : '.json_encode($data);              
            Log::info($msg);
            $this->sharedService->logs($msg);

            echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully update pengurus: '.$e->getMessage());       
            echo json_encode(array('status' => false, 'message' => $e->getMessage()));
        }
        
    }

}

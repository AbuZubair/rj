<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Shu;

use Illuminate\Http\Request;

class ShuController extends Controller
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
        return view('shu.index');
    }

    public function persentase(Request $request)
    {
        return view('shu.persentase.index');
    }

    public function pengurus(Type $var = null)
    {
        return view('shu.pengurus.index');
    }

    public function persentaseGetData()
    {
        $query = Shu::mainQuery();
        $data = $query
                ->where('parameter.param','point_shu')
                ->where('parameter.is_active','Y')
                ->select(DB::raw('parameter.*,param.value as persentase'))
                ->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Proses berhasil dilakukan', 'data' => $data));
    }

    public function persentaseSaveData(Request $request)
    {
        try {
            $data = $request->post();
            $keys = array_keys($data);
            for ($i=0; $i < count($keys); $i++) { 
                $key = $keys[$i];
                DB::table('parameter')
                    ->where('param','persen_shu')
                    ->where('param1',$key )
                    ->update(['value' => $data[$key], 'label' => $data[$key]]);
            }
            $msg = Auth::user()->getUsername(). ' update persentase shu succesfully : '.json_encode($data);              
            Log::info($msg);
            $this->sharedService->logs($msg);

            echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully update persentase shu: '.$e->getMessage());       
            echo json_encode(array('status' => false, 'message' => $e->getMessage()));
        }
        
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = Shu::mainQuery();
            $data = $query
                ->leftJoin(
                    DB::raw("(select point_shu, jumlah, year from `shu` where year = (select MAX(year) from shu)) `shu`"), 
                    'shu.point_shu', '=', 'parameter.value'
                )
                ->where('parameter.param','point_shu')
                ->where('parameter.is_active','Y')
                ->select(DB::raw('parameter.*,param.value as persentase, shu.jumlah as total, shu.year'))
                ->get();
            return Datatables::of($data)               
                ->make(true);
        }
    }

    public function getDataPengurus(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('shu_pengurus')
                    ->leftJoin('anggota', 'anggota.no_anggota', '=', 'shu_pengurus.no_anggota')
                    ->leftJoin(
                        DB::raw("(select label,value from `parameter` where `parameter`.`param` = 'kepengurusan') `param`"), 
                        'param.value', '=', 'shu_pengurus.pengurus'
                    );
            if($request->input('searchYear')!='')$query->where('shu_pengurus.year',$request->input('searchYear'));
            $data = $query
                    ->orderBy('shu_pengurus.id')
                    ->get();
            return Datatables::of($data)               
                ->make(true);
        }
    }
  
}

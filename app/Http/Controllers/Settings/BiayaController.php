<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Anggota;

use Illuminate\Http\Request;

class BiayaController extends Controller
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
        return view('settings.biaya.index');
    }

    public function getData(Request $request)
    {
        $included = ['uang_masuk','daftar_ulang','spp'];
        $data = DB::table('parameter')
                ->whereIn('parameter.param',$included)
                ->orderByDesc('param')
                ->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Proses berhasil dilakukan', 'data' => $data));
    }

    public function save(Request $request)
    {
        try {
            $data = $request->post();
            $keys = array_keys($data);
            for ($i=0; $i < count($keys); $i++) { 
                $key = explode("-", $keys[$i]);
                $key_1 = $key[0];
                $key_2 = isset($key[1]) ? $key[1] : '';
                $value = (int)str_replace(".","",$data[$keys[$i]]);
                if($value == 0){
                    echo json_encode(array('status' => false, 'message' => 'Nilai tidak boleh 0'));
                    die();
                }
                DB::table('parameter')
                    ->where('param',$key_1)
                    ->where('param1',$key_2)
                    ->update(['value' => $value, 'label' => $value, 'updated_date' => date("Y-m-d H:i:s") ]);               
            }
            $msg = Auth::user()->getUsername(). ' update settings succesfully : '.json_encode($data);              
            Log::info($msg);
            $this->sharedService->logs($msg);

            echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully update settings: '.$e->getMessage());       
            echo json_encode(array('status' => false, 'message' => $e->getMessage()));
        }
        
    }

}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Anggota;

use Illuminate\Http\Request;

class SettingsController extends Controller
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
        return view('settings.index');
    }

    public function getData(Request $request)
    {
        $included = ['iuran','limit_kredit'];
        $data = DB::table('parameter')
                ->whereIn('parameter.param',$included)
                ->orderBy('id')
                ->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Proses berhasil dilakukan', 'data' => $data));
    }

    public function save(Request $request)
    {
        try {
            $data = $request->post();
            $keys = array_keys($data);
            for ($i=0; $i < count($keys); $i++) { 
                $key = $keys[$i];
                $value = (int)str_replace(".","",$data[$key]);
                if($value == 0){
                    echo json_encode(array('status' => false, 'message' => 'Nilai tidak boleh 0'));
                    die();
                }
                if($key == "limit_kredit"){
                    $limit_before = DB::table('parameter')->where('parameter.param','limit_kredit')->select('value')->first();
                    Anggota::where(['is_active' => 'Y', 'limit_kredit' => $limit_before->value])->update(['limit_kredit'=>$value]);
                }
                if(!str_contains($key, 'input')){
                    DB::table('parameter')
                        ->where('param',$key)
                        ->update(['value' => $value, 'label' => $value, 'updated_date' => date("Y-m-d H:i:s") ]);
                }                
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

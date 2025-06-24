<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Anggota;
use App\User;
use App\Http\Requests\Master\AnggotaRequest;

use Illuminate\Http\Request;

class AnggotaController extends Controller
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
        return view('master.anggota.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data =  Anggota::getData($request);
            return Datatables::of($data)               
                ->make(true);
        }
    }

    public function add()
    {
        $type = 'Add';
        return view('master.anggota.form',compact('type'));
    }

    public function edit(Request $req)
    {
        $id = $req->get('id');
        $data = Anggota::find($id)->toArray();
        echo json_encode(array('status' => 200, 'message' => 'Process Succesfully', 'data' => $data));
    }

    public function crud(AnggotaRequest $request, $req)
    {
        $is_update = false;
        try{
            $data = new Anggota;
            if($request->input('id') != ''){
                $data = Anggota::find($request->input('id'));
                $data->updated_by = Auth::user()->getUsername();
                $data->join_date = date('Y-m-d');
                $is_update = true;
            }else{
                // $max = Anggota::getLatestNoAnggota();           
                // $data->no_anggota = 'P' . str_pad($max->max + 1, 3, '0', STR_PAD_LEFT);
                $data->no_anggota = $request->input('no_anggota');
                $data->created_by = Auth::user()->getUsername();
                if($request->input('is_active') == 'N')$data->relieve_date = date('Y-m-d');
            }
            $data->fullname = $request->input('fullname');
            $data->grade = $request->input('grade');
            $data->divisi = $request->input('divisi');
            $data->department = $request->input('department');
            $data->is_active = $request->input('is_active');
            $data->join_date = $request->input('join_date'); 
            $data->email = $request->input('email'); 
            $data->limit_kredit = (int)str_replace(".","",$request->input('limit_kredit'));  
            if($data->save()){  
                $type = ($request->input('id') == '')?' save':' update';
                $msg = Auth::user()->getUsername(). $type.' anggota succesfully : '.json_encode($data);              
                Log::info($msg);
                $this->sharedService->logs($msg);
                $userQuery = User::where('no_anggota', $data->no_anggota);
                if($userQuery->doesntExist()){
                    $this->saveUser($data);
                }
                if($is_update){
                    $user = $userQuery->first();
                    if($user != null){
                        $user->email = $data->email;
                        $user->save();
                    }
                }
                echo json_encode(array('status' => 200, 'message' => 'Process Succesfully'));
            }
        }
        catch (Exception $e){   
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully'.($request->input('id') == '')?' save':' update'. ' anggota: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => 'Proccess Unsuccessfully'));
        }
            
    }

    private function saveUser($anggota)
    {
        try{
            $data = new User;
            $data->username = $anggota->no_anggota;
            $data->created_by = Auth::user()->getUsername();
            $parts = explode(" ", $anggota->fullname);
            if(count($parts) > 1) {
                $data->first_name = implode(" ", $parts);
                $data->last_name = array_pop($parts);
            }
            else
            {
                $data->first_name = $anggota->fullname;
                $data->last_name = "";
            }
            $data->role = 1;
            $data->no_anggota = $anggota->no_anggota;
            $data->email = $anggota->email;
            if(strlen($anggota->no_anggota) > 0){
                $data->password = Hash::make('kopkar'.$anggota->no_anggota);
            }
            
            if($data->save()){  
                $msg = Auth::user()->getUsername().' save user succesfully : '.json_encode($data);              
                Log::info($msg);
                $this->sharedService->logs($msg);
                return true;
            }
        }
        catch (Exception $e){   
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully save User: '.$e->getMessage());       
            return false;
        }
    }

    public function delete(Request $request)
    {
        try {      
            $id = $request->post('data');
            $data = Anggota::whereIn('id', $id);            
            $msg = Auth::user()->getUsername(). ' delete anggota succesfully : '.json_encode($data->get()->toArray());              
            Log::info($msg);
            $this->sharedService->logs($msg);
            $data->delete();
            echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan'));
        }catch (Exception $e){
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully delete anggota: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => $e->getMessage()));
        }
        
    }

    public function getDropdown(Request $request)
    {
        $data = Anggota::where('is_active','Y')->orderByDesc('created_date')->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

    public function getParams(Request $request)
    {
        $data =  new \stdClass();
        $data->department = $this->sharedService->getParamDropdown('department');
        $data->divisi = $this->sharedService->getParamDropdown('divisi');
        $data->grade = $this->sharedService->getParamDropdown('grade');
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => $data));
    }

}

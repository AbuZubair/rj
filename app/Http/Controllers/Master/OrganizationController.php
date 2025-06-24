<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\Master\OrganizationRequest;

use Illuminate\Http\Request;

class OrganizationController extends Controller
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
        return view('master.organization.index');
    }

    public function getData(Request $request)
    {
        $included = ['divisi','department','grade'];
        $query = DB::table('parameter');
        if($request->get('param')==''){
            $query->whereIn('parameter.param',$included);
        }else{
            $query->where('parameter.param',$request->get('param'));
        }
        $data = $query->orderByDesc('id')->get()->toArray();
        echo json_encode(array('status' => true, 'message' => 'Proses berhasil dilakukan', 'data' => $data));
    }

    public function add()
    {
        $type = 'Add';
        return view('master.organization.form',compact('type'));
    }

    public function edit(Request $req)
    {
        $id = $req->get('id');
        $data = DB::table('parameter')->where('parameter.id',$id)->get()->first();
        echo json_encode(array('status' => 200, 'message' => 'Process Succesfully', 'data' => $data));
    }

    public function crud(OrganizationRequest $request)
    {
        $status = null;
        try{
            $data = array(
                "label" => $request->input('label'),
                "param" => $request->input('param')
            );
            if($request->input('id') != ''){
                $query = DB::table('parameter')->where('id',$request->input('id'));
                $status =  $query->update($data);
            }else{
                $data["value"] = Str::uuid();
                $status = DB::table('parameter')->insert($data);
            }
            if($status){  
                $type = ($request->input('id') == '')?' save':' update';
                $msg = Auth::user()->getUsername(). $type.' organisasi succesfully : '.json_encode($data);              
                Log::info($msg);
                $this->sharedService->logs($msg);
                echo json_encode(array('status' => 200, 'message' => 'Process Succesfully'));
            }
        }
        catch (Exception $e){   
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully'.($request->input('id') == '')?' save':' update'. ' organisasi: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => 'Proccess Unsuccessfully'));
        }
            
    }

    public function delete(Request $request)
    {
        try {      
            $id = $request->post('data');
            $data =DB::table('parameter')->whereIn('id', $id);            
            $msg = Auth::user()->getUsername(). ' delete organisasi succesfully : '.json_encode($data->get()->toArray());              
            Log::info($msg);
            $this->sharedService->logs($msg);
            $data->delete();
            echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan'));
        }catch (Exception $e){
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully delete organisasi: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => $e->getMessage()));
        }
        
    }

}

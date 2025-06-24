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
use App\Coa;
use App\Http\Requests\CoaRequest;

class CoaController extends Controller
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
        return view('coa.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $order = Coa::generateOrder();
            $data = Coa::where('is_sum','N')->orderByRaw($order)->get();
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
        $data = Coa::find($id)->toArray();
        echo json_encode(array('status' => 200, 'message' => 'Process Succesfully', 'data' => $data));
    }

    public function crud(CoaRequest $request, $req)
    {

        try{
            $data = new Coa;
            if($request->input('id') != ''){
                $data = Coa::find($request->input('id'));
                $data->updated_by = Auth::user()->getUsername();
            }else{
                $data->created_by = Auth::user()->getUsername();
            }
            $data->coa_code = $request->input('coa_code');
            $data->coa_name = $request->input('coa_name');
            $data->coa_level = $request->input('coa_level');
            $data->coa_parent = $request->input('coa_parent');
            if($request->input('coa_parent') == 'C.7')$data->rumus_ending_balance = 2;      
            if($data->save()){  
                $type = ($request->input('id') == '')?' save':' update';
                $msg = Auth::user()->getUsername(). $type.' coa succesfully : '.json_encode($data);              
                Log::info($msg);
                $this->sharedService->logs($msg);
                echo json_encode(array('status' => 200, 'message' => 'Process Succesfully'));
            }
        }
        catch (Exception $e){   
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully'.($request->input('id') == '')?' save':' update'. ' COA: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => 'Proccess Unsuccessfully'));
        }
            
    }

    public function delete(Request $request)
    {
        try {      
            $id = $request->post('data');
            $data = Coa::whereIn('id', $id);            
            $msg = Auth::user()->getUsername(). ' delete COA succesfully : '.json_encode($data->get()->toArray());              
            Log::info($msg);
            $this->sharedService->logs($msg);
            $data->delete();
            echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan'));
        }catch (Exception $e){
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully delete COA: '.$e->getMessage());       
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
        $order = Coa::generateOrder();
        echo json_encode(array('status' => true, 'message' => 'Prosess berhasil dilakukan', 'data' => Coa::where('is_sum','N')->orderByRaw($order)->get()->toArray()));
    }
}

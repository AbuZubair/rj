<?php

namespace App\Http\Controllers;

use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Staff;
use App\Http\Requests\UserRequest;

class UserController extends Controller
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
        return view('user.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = User::latest()->where('username','!=',Auth::user()->getUsername())->get();
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
        $data = User::find($id)->toArray();
        echo json_encode(array('status' => 200, 'message' => 'Process Succesfully', 'data' => $data));
    }

    public function crud(UserRequest $request)
    {

        try{
            $data = new User;
            if($request->input('id') != ''){
                $data = User::find($request->input('id'));
                $data->updated_by = Auth::user()->getUsername();
            }else{
                $fn = str_replace(' ', '', $request->input('first_name'));
                $ln = str_replace(' ', '', $request->input('last_name'));
                $check = ($request->input('last_name'))?strtolower($fn). '.' .strtolower($ln):strtolower($fn);            
                $i=0;
                while (!$check) {
                    $i++;
                    $check = $this.checkUsername($check).$i;            
                }
                $data->username = ($request->input('role') == '1')?$request->input('nip'):$check;
                $data->created_by = Auth::user()->getUsername();
            }
            $data->first_name = $request->input('first_name');
            $data->last_name = $request->input('last_name');
            $data->phone_number = $request->input('phone_number');
            $data->role = $request->input('role');
            $data->nip = $request->input('nip');
            $data->email = $request->input('email');
            if(strlen($request->get('password')) > 0){
                $data->password = Hash::make($request->get('password'));
            }
            
            if($data->save()){  
                $type = ($request->input('id') == '')?' save':' update';
                $msg = Auth::user()->getUsername(). $type.' user succesfully : '.json_encode($data);              
                Log::info($msg);
                $this->sharedService->logs($msg);
                if($data->role == 1){
                    $anggota = Staff::where('nip', $data->nip)->first();
                    if($anggota != null){
                        $anggota->email = $data->email;
                        $anggota->save();
                    }
                }
                echo json_encode(array('status' => 200, 'message' => 'Process Succesfully'));
            }
        }
        catch (Exception $e){   
            Log::error($e->getMessage());  
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully'.($request->input('id') == '')?' save':' update'. ' User: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => 'Proccess Unsuccessfully'));
        }
            
    }

    public function checkUsername($username)
    {
        $user = User::where('username', $username)->first();
        if($user === null) return true;
            return false;
    }

    public function delete(Request $request)
    {
        try {      
            $id = $request->post('data');
            $user = User::whereIn('id', $id);            
            $msg = Auth::user()->getUsername(). ' delete user succesfully : '.json_encode($user->get()->toArray());              
            Log::info($msg);
            $this->sharedService->logs($msg);
            $user->delete();
            echo json_encode(array('status' => 200, 'message' => 'Prosess berhasil dilakukan'));
        }catch (Exception $e){
            Log::error($e->getMessage());
            $this->sharedService->logs(Auth::user()->getUsername().' unsuccessfully delete user: '.$e->getMessage());       
            echo json_encode(array('status' => 301, 'message' => $e->getMessage()));
        }
        
    }
}

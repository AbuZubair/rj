<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Library\Services\Shared;
use App\User;

class ChangePasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Change Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for change default
    | password
    |
    */

    /**
     * Create a new controller instance.
     *
     */

    private $sharedService;

    public function __construct(Shared $sharedService)
    {
        $this->middleware('auth');  
        $this->sharedService = $sharedService;
    }

    public function index(){        
        return view('auth.change-password');
    }

    public function save(ChangePasswordRequest $request){        
        $id = Auth::user()->getId();
        $password = Hash::make($request->input('password'));
        $user = User::find($id);
        $user->password = $password;
        if($user->save()){  
            $msg = Auth::user()->getUsername().' has been change his password';
            Log::info($msg);
            $this->sharedService->logs($msg);       
            echo json_encode(array('status' => 200, 'message' => 'Process Succesfully, please re-login'));
        }
    }
}

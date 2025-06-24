<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Library\Services\Shared;

class SignOutController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Sign Out Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for sign out from application
    |
    */
    private $sharedService;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct(Shared $sharedService)
    {
        $this->middleware('auth');
        $this->sharedService = $sharedService;
    }

    public function index(){
        $this->sharedService->logs(Auth::user()->getusername().' has been logout');
        Auth::logout();
        session()->flush();       
        return redirect(route('login'));
    }
}

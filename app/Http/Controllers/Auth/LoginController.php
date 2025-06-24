<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\SignInRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Support\Facades\Log;
use App\Library\Services\Shared;
use App\Library\Model\Model;
use App\User;
use App\Anggota;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Sign Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    private $model;
    private $sharedService;

    /**
     * Create a new controller instance.
     *
     */

    public function __construct(Shared $sharedService,Model $model)
    {
        $this->middleware('guest');
        $this->model = $model;
        $this->sharedService = $sharedService;
    }

    public function index(){
        return view('auth.login');
    }

    public function onSignedIn(SignInRequest $request){
        $credentials = array(
            'username' => $request->input('username'),
            'password' => $request->input('password'),
        );
       
        $fieldType = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $check_anggota = Anggota::where('no_anggota', $request->input('username'))->first();
        if($check_anggota!=null){
            if($check_anggota->is_active=='N'){
                return redirect(route('login'))->withInput($request->except('password'))->withErrors([
                    'invalid' => 'Anggota sudah tidak akftif'
                ]);
            }
        }
        if (Auth::attempt(array($fieldType => $request->input('username'), 'password' => $request->input('password')))) {
            $msg = Auth::user()->getusername().' has been login';
            Log::info($msg);            
            $this->sharedService->logs($msg);
            return redirect(route('dashboard'));
        } else {
            Log::info($request->input('username').' try to login but failed with message '.trans('validation.password'));
            return redirect(route('login'))->withInput($request->except('password'))->withErrors([
                'password' => 'Password salah'
            ]);
        }
       
    }

    public function forgotPasssword(ForgotPasswordRequest $request)
    {
        try {
            $data = User::where('email',$request->input('email'))->first();
            $new_password = $this->randomPassword();
            $data->password = Hash::make($new_password);
            if($data->save()){
                $msg = $data->username.' reset password succesfully';              
                Log::info($msg);
                $this->sharedService->logs($msg);

                $email = array(
                    'to' => $request->input('email'),
                    'subject' => 'Reset Password'
                );

                $data->new_password = $new_password;
                $this->sharedService->sendEmail($email,'mail_resetpassword',$data);

                return redirect(route('login'))->with('success','Reset password berhasil, silahkan check email anda');
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());  
            $this->sharedService->logs('reset password unsuccessfully: '.$e->getMessage());       
            return redirect(route('login'))->withInput($request->input())->withErrors($e);
        }
    }

    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

}

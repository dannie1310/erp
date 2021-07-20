<?php

namespace App\Http\Controllers\Auth;

use App\Models\IGH\Usuario;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\AuthenticatesIghUsers;
use Illuminate\Support\Facades\Session;
use App\Models\SEGURIDAD_ERP\Google2faSecret;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesIghUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        
        if(array_key_exists('clave_confirmacion', $request->all())){
            if($this->actualizarPassword($request)){
                return route('login');
            }
        }else{
            $this->validateLogin($request);
        }
        

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        if($this->validaPasswordGenerico($request)){
            return view('auth.cambio_contrasena_temporal');
        }
        if ($this->attemptLogin($request)) {
            if (! auth()->user()->google2faSecret) {
                $g = new GoogleAuthenticator();
                $secret = $g->generateSecret();
                Google2faSecret::query()->create([
                    'secret' => $secret,
                    'id_user' => auth()->id()
                ]);
            return $this->sendLoginResponse($request);
            }
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function actualizarPassword($request){
        $credenciales = Session::get('credenciales');
        $usuario = Usuario::where('usuario', '=', $credenciales['usuario'])->where('clave', '=', md5($credenciales['clave']))->first();
        $usuario->clave = $request['clave_nueva'];
        $usuario->pide_cambio_contrasenia = 0;
        $usuario->save();
        return true;
    }
    
    private function validaPasswordGenerico($request){
        Session::put('credenciales', $request->all());
        $usuario = Usuario::where('usuario', '=', $request['usuario'])->where('clave', '=', md5($request['clave']))->first();
        if($usuario && $usuario->pide_cambio_contrasenia == 1){
            return true;
        }
        return false;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Google\Authenticator\GoogleAuthenticator;

class TwoFactorAuth
{

    protected $google;

    public function __construct(GoogleAuthenticator $google)
    {
        $this->google = $google;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $code = $request->code;
        if ($code) {
            if($this->google->checkCode(auth()->user()->google2faSecret->secret, $code)) {
                return $next($request);
            }
            abort(400, 'Código de Verificación no válido');
        }
        abort(400, 'No se envió el código de verificación');
    }
}

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
        return $next($request);

        $code = $request->code;
        if ($code) {
            if($this->google->checkCode($code)) {
                return $next($request);
            }
            abort(400, 'Código de Verificación no válido');
        }
        abort(400, 'No se envió el código de verificación');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Permiso
{
    const DELIMITER = '|';

    /**
     * @var Guard
     */
    protected $auth;

    /**
     * Permiso constructor.
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permisos)
    {
        if (!is_array($permisos)) {
            $permisos = explode(self::DELIMITER, $permisos);
    }
        if ($this->auth->guest() || !$request->user()->can($permisos)) {
            abort(403, 'No cuentas con los permisos necesarios para realizar la acción solicitada');
        }
        return $next($request);
    }
}
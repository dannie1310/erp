<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class PermisoGlobal
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
     * @param $request
     * @param Closure $next
     * @param $permisos
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function handle($request, Closure $next, $permisos, $requireAll = false)
    {
        if (!is_array($permisos)) {
            $permisos = explode(self::DELIMITER, $permisos);
        }

        if ($this->auth->guest() || !$request->user()->can($permisos, $requireAll, true)) {
            abort(403, 'No cuentas con los permisos necesarios para realizar la acción solicitada');
        }

        if ($google_auth = \App\Models\SEGURIDAD_ERP\Permiso::query()->whereIn('name', $permisos)->where('requiere_autorizacion', '=', true)->first()) {
            return app(TwoFactorAuth::class)->handle($request, function ($request) use ($next) {
                return $next($request);
            });
        }

        return $next($request);
    }
}

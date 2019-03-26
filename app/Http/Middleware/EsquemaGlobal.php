<?php

namespace App\Http\Middleware;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use Closure;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EsquemaGlobal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $obra =  Obra::query()->find(Context::getIdObra());

        if (isset($obra->configuracion->esquema_permisos) && $obra->configuracion->esquema_permisos == 1) {
            return $next($request);
        } else {
            throw new BadRequestHttpException('El esquema actual de permisos de la obra es personalizado o no está configurado');
        }
    }
}
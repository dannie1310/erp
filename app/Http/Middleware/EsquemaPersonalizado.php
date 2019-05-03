<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Garrido
 * Date: 26/03/2019
 * Time: 18:24
 */

namespace App\Http\Middleware;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use Closure;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class EsquemaPersonalizado
{


    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $obra =  Obra::query()->find(Context::getIdObra());

        if (isset($obra->configuracion->esquema_permisos) && $obra->configuracion->esquema_permisos == 2) {
            return $next($request);
        } else {
            throw new BadRequestHttpException('El esquema actual de permisos de la obra es global o no está configurado');
        }
    }

}
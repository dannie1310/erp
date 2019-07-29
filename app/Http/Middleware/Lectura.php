<?php


namespace App\Http\Middleware;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Proyecto;
use Closure;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Lectura
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
        $proyectos = Proyecto::query()->get()->where('base_datos','=',Context::getDatabase());

        foreach($proyectos as $proyecto){
            $tipo_obra = ConfiguracionObra::query()->get()->where('id_proyecto','=',$proyecto->id)
                ->where('id_obra','=',Context::getIdObra());
            foreach ($tipo_obra as $tipo){
                if($tipo->consulta == 1 || $tipo->tipo_obra == 2){
                    abort(400, 'El estatus en el que se encuentra la obra no permite ejecutar esta acción');
                }
                return $next($request);
            }
        }
    }

}
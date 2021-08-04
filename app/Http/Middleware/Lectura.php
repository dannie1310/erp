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

    public function handle($request, Closure $next, $permisos)
    {
        if(Context::getIdObra())
        {
            $proyectos = Proyecto::query()->where('base_datos','=',Context::getDatabase())->first();

            $tipo_obra = ConfiguracionObra::query()->where('id_proyecto','=',$proyectos->id)
                ->where('id_obra','=',Context::getIdObra())->first();

            $obra = Obra::query()->where('id_obra','=',Context::getIdObra())->first();

            if($tipo_obra->consulta == 1 || $tipo_obra->tipo_obra == 2 || $obra->tipo_obra == 2){
                $consulta = \App\Models\SEGURIDAD_ERP\Permiso::query()->whereIn('name', $permisos)->where('es_de_consulta', '=', false)->first();
                if($consulta){
                    abort(400, 'El estatus en el que se encuentra la obra no permite ejecutar esta acción.');
                }

            }
        }

    }

}

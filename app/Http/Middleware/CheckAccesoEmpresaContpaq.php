<?php

namespace App\Http\Middleware;

use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaUsuario;
use Closure;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CheckAccesoEmpresaContpaq
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
        $id_empresa = $request->id_empresa?$request->id_empresa:$request->id;
        $id_usuario = auth()->user()->idusuario;
        $empresa_usuario = EmpresaUsuario::where("id_empresa","=",$id_empresa)
            ->where("id_usuario","=",$id_usuario)->get()->count();

        if($empresa_usuario == 1){
            return $next($request);
        }
        throw new BadRequestHttpException('No tiene permitido el acceso a esta empresa de Contpaq');

    }
}

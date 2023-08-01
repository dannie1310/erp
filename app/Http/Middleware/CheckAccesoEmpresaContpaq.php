<?php

namespace App\Http\Middleware;

use App\Models\CTPQ\GeneralesSQL\Usuario;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
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
        if(!$id_empresa>0)
        {
            throw new BadRequestHttpException('No se detecta un identificador de empresa válido en la petición');
        }

        $id_usuario = auth()->user()->idusuario;
        $empresa_usuario = EmpresaUsuario::where("id_empresa","=",$id_empresa)
            ->where("id_usuario","=",$id_usuario)->get()->count();

        $usuario = strtoupper(auth()->user()->usuario);
        $empresa = Empresa::find($id_empresa);
        $id_empresa_contpaq = $empresa ?$empresa->IdEmpresaContpaq:0;

        $id_usuario_contpaq = Usuario::where("codigo","=",$usuario)
            ->where("EsBaja","=",0)
            ->pluck("Id")
            ->first();

        $empresa_contpaq_usuario = \App\Models\CTPQ\GeneralesSQL\EmpresaUsuario::where("IdUsuario","=",$id_usuario_contpaq)
        ->where("IdEmpresa","=",$id_empresa_contpaq)
        ->get()
        ->count();

        if($empresa_usuario > 0 || $empresa_contpaq_usuario >0){
            return $next($request);
        }
        throw new BadRequestHttpException('No tiene permitido el acceso a esta empresa de Contpaq');

    }
}

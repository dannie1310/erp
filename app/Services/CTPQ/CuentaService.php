<?php


namespace App\Services\CTPQ;


use App\Jobs\ProcessAsociacionCuentasContpaqProveedoresSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCuentaProveedor;
use App\Repositories\CTPQ\CuentaRepository;
use Exception;
use App\Models\CTPQ\Cuenta;
use App\Models\CTPQ\Empresa;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class CuentaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaService constructor.
     * @param Cuenta $model
     */
    public function __construct(Cuenta $model)
    {
        $this->repository = new CuentaRepository($model);
    }

    public function index($data)
    {
        $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);
        $empresa = Empresa::find($empresaLocal->IdEmpresaContpaq);
        DB::purge('cntpq');
        \Config::set('database.connections.cntpq.database',$empresa->AliasBDD);
        return $this->repository->all($data);
    }

    public function paginate($data){
        try{
            $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);

            $empresa = Empresa::find($empresaLocal->IdEmpresaContpaq);
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
            $cuenta = $this->repository;

            if (isset($data['codigo'])) {
                if ($data['codigo'] != "") {
                    $cuenta->where([['Codigo', 'like', '%'.$data['codigo'].'%' ]]);
                }
            }

            if (isset($data['nombre'])) {
                if ($data['nombre'] != "") {
                    $cuenta->where([['Nombre', 'like', '%'.strtoupper($data['nombre']).'%']]);
                }
            }
            return $cuenta->paginate($data);
        }catch(Exception $e){
            abort(500,"Error de lectura a la base de datos: ".Config::get('database.connections.cntpq.database').". \n \n Favor de contactar a soporte a aplicaciones.");
            throw $e;
        }
    }

    public function asociarCuenta($data){
        $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa_contpaq"]);
        $data['id_empresa_contpaq'] = $empresaLocal->IdEmpresaContpaq;
        $empresa = Empresa::find($empresaLocal->IdEmpresaContpaq);
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
        return $this->repository->show($data['id_cuenta_contpaq'])->asociarCuenta($data);
    }

    public function solicitaAsociacionProveedor($data){
        /*ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);
        $data['id_empresa'] = $empresaLocal->IdEmpresaContpaq;
        $empresa = \App\Models\CTPQ\Empresa::find($empresaLocal->IdEmpresaContpaq);
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $empresa->AliasBDD);

        $cuentas= $this->repository->getCuentasPasivo($data["id_empresa"]);

        $idistribucion = 0;
        foreach($cuentas as $cuenta){
            //$cuenta->procesarAsociacionProveedor($empresaLocal->IdEmpresaContpaq);
            ProcessAsociacionCuentasContpaqProveedoresSAT::dispatch($cuenta, $empresaLocal->IdEmpresaContpaq)->onQueue("q".$idistribucion);
            $idistribucion ++;
            if($idistribucion==5){
                $idistribucion=0;
            }
        }*/
        $solicitud =SolicitudAsociacionCuentaProveedor::getSolicitudActiva($data["id_empresa"]);
        if(!$solicitud){
            $solicitud = $this->generaPeticionesDeAsociacion($data);
            $datos_solicitud = [
                "folio" =>$solicitud->id,
                "usuario_inicio" =>$solicitud->usuario->nombre_completo,
                "fecha_hora_inicio"=>$solicitud->fecha_hora_inicio_format,
                "mensaje" =>"Proceso de asociación generado éxitosamente, se le enviará un correo al finalizar",
                "icon" =>"success"
            ];
        } else {
            $datos_solicitud = [
                "folio" =>$solicitud->id,
                "usuario_inicio" =>$solicitud->usuario->nombre_completo,
                "fecha_hora_inicio"=>$solicitud->fecha_hora_inicio_format,
                "mensaje" =>"Existe un proceso activo de asociación de cuentas a proveedores para esta empresa, favor de esperar a que termine",
                "icon" =>"warning"
            ];
        }
        return $datos_solicitud;
    }

    public function generaPeticionesDeAsociacion($data)
    {
        $solicitud = $this->repository->generaSolicitudAsociacion($data);
        $cuentas = $this->repository->getCuentas($data);

        foreach($cuentas as $cuenta){
            $data = [
                "id_solicitud_asociacion" => $solicitud->id,
                "id_empresa_contpaq" => $solicitud->id_empresa_contpaq,
                "base_datos" => $solicitud->base_datos,
                "nombre_empresa" => $solicitud->nombre_empresa,
                "id_cuenta_contpaq" => $cuenta->Id,
                "nombre_cuenta_original"=>$cuenta->Nombre,
            ];
            $this->repository->generaPartidasAsociacion($data);
        }
        $idistribucion = 0;
        foreach($solicitud->partidas as $partida){
            ProcessAsociacionCuentasContpaqProveedoresSAT::dispatch($partida)->onQueue("q".$idistribucion);
            //$partida->procesarAsociacion();
            $idistribucion ++;
            if($idistribucion==10){
                $idistribucion=0;
            }
        }
        return $solicitud;
    }

    public function eliminarAsociacion($data){
        $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);
        $data['id_empresa_contpaq'] = $empresaLocal->IdEmpresaContpaq;
        $empresa = Empresa::find($empresaLocal->IdEmpresaContpaq);
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
        return $this->repository->show($data['id_cuenta'])->eliminarAsociacion();
    }

    public function validaCuenta($alias_bdd, $cuenta)
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $alias_bdd);
        $this->repository->where([['Codigo','=', $cuenta]]);
        $cuenta = $this->repository->first();
        $this->repository->where([['estado', '=', 0]]);
        if($cuenta)
        {
            return true;
        }
        return false;
    }
}

<?php


namespace App\Services\CTPQ;


use App\Jobs\ProcessAsociacionCuentasContpaqProveedoresSAT;
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
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);
        $data['id_empresa'] = $empresaLocal->IdEmpresaContpaq;
        $empresa = \App\Models\CTPQ\Empresa::find($empresaLocal->IdEmpresaContpaq);
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $empresa->AliasBDD);

        $cuentas= $this->repository->getCuentasPasivo($data["id_empresa"]);

        $idistribucion = 0;
        foreach($cuentas as $cuenta){
            $cuenta->procesarAsociacionProveedor($empresaLocal->IdEmpresaContpaq);
            /*ProcessAsociacionCuentasContpaqProveedoresSAT::dispatch($cuenta, $empresaLocal->IdEmpresaContpaq)->onQueue("q".$idistribucion);
            $idistribucion ++;
            if($idistribucion==5){
                $idistribucion=0;
            }*/
        }
    }

}

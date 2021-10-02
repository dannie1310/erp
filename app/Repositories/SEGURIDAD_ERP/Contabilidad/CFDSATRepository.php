<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 04:18 PM
 */

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;

use App\Informes\CFDEmpresaMes;
use App\Informes\CFDICompleto;
use App\Informes\Fiscal\InformeSATLP;
use App\Models\SEGURIDAD_ERP\catCFDI\TipoComprobante;
use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Documentacion\CtgTipoTransaccion;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgNoLocalizado;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Repositories\SEGURIDAD_ERP\Fiscal\CtgNoLocalizadoRepository;
use Illuminate\Support\Facades\DB;

class CFDSATRepository extends Repository implements RepositoryInterface
{
    public function __construct(CFDSAT $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrar(array $datos)
    {
        return $this->model->registrar($datos);

    }

    public function getArchivoSQL($archivo)
    {
        return DB::raw("CONVERT(VARBINARY(MAX), '" . $archivo . "')");
    }

    public function getRFCReceptoras()
    {
        $empresas = EmpresaSAT::all()->pluck("rfc")->toArray();
        return $empresas;
    }

    public function getIdEmpresa($datos_receptor){
        try{
            $empresa = EmpresaSAT::where("rfc","=",$datos_receptor["rfc"])
                ->first();
            $salida = null;

            if($empresa){
                return $empresa->id;
            } else {
                /*$empresa = EmpresaSAT::create(
                    ["rfc"=>$datos_receptor["rfc"], "razon_social"=>$datos_receptor["nombre"]]
                );
                return $empresa->id;*/
                return -1;
            }
        } catch (\Exception $e){
            dd($datos_receptor);
        }

    }

    public function iniciaCarga($nombre_archivo){
        return $this->model->carga()->create(["nombre_archivo_zip"=>$nombre_archivo]);
    }

    public function finalizaCarga($carga){
        EFOS::actualizaEFOS(null,$carga);
    }

    public function getIdProveedorSAT($datos, $id_empresa){

        $proveedor = ProveedorSAT::where("rfc","=",$datos["rfc"])
            ->first();
        if($proveedor){
            return $proveedor->id;
        }  else{
            if($id_empresa>0){
                $proveedor = ProveedorSAT::create(
                    $datos
                );
                return $proveedor->id;
            }else{
                return null;
            }

        }
    }

    public function getProveedorSAT($datos, $id_empresa){

        $proveedor = ProveedorSAT::where("rfc","=",$datos["rfc"])
            ->first();
        if($proveedor){
            return ["id_proveedor" => $proveedor->id, "nuevo" => 0];
        }  else{
            if($id_empresa>0){
                $proveedor = ProveedorSAT::create(
                    $datos
                );
                return ["id_proveedor" => $proveedor->id, "nuevo" => 1];
            }else{
                return null;
            }

        }
    }

    public function getTipoTransaccion($id_tipo_transaccion)
    {
        $tipo_transaccion = CtgTipoTransaccion::find($id_tipo_transaccion);
        return $tipo_transaccion;
    }

    public function getTipoComprobante($tipo_transaccion)
    {
        return TipoComprobante::where("tipo_comprobante", "=", $tipo_transaccion)->first();
    }

    public function getEstadoEFO($rfc)
    {
        $efo = DB::connection("seguridad")->table("Finanzas.ctg_efos")
            ->where("rfc","=",$rfc)
            ->first();
        if($efo){
            return $efo->estado;
        }
        else{
            return null;
        }
    }

    public function validaExistencia($uuid)
    {
        $cfd = CFDSAT::where("uuid","=", $uuid)->first();
        return $cfd;
    }

    public function getInformeEmpresaMes()
    {
        $informe["informe"] = CFDEmpresaMes::get();
        return $informe;
    }

    public function getInformeCompleto()
    {
        $informe["informe"] = CFDICompleto::get();
        return $informe;
    }

    public function actualizaNoLocalizados(CargaCFDSAT $cargaCFDSAT)
    {
        $ctgNoLocalizadoModel = new CtgNoLocalizado();
        $ctgNoLocalizadoRepository = new CtgNoLocalizadoRepository($ctgNoLocalizadoModel);
        $ctgNoLocalizadoRepository->actualizaNoLocalizado($cargaCFDSAT);
    }

    public function obtenerInformeSATLP2020($data)
    {
        $informe["informe"] = InformeSATLP::get($data);
        return $informe;
    }

    public function obtenerCuentasInformeSATLP2020($data)
    {
        $informe["informe"] = InformeSATLP::getCuentas($data);
        return $informe;
    }

    public function obtenerMovimientosCuentasInformeSATLP2020($data)
    {
        $informe["informe"] = InformeSATLP::getMovimientos($data);
        return $informe;
    }

    public function getListaCFDI($id_proveedor, $fecha_inicial, $fecha_final, $asociada_contpaq, $empresas)
    {

        $cfdi = InformeSATLP::getListaCFDI($id_proveedor, $fecha_inicial, $fecha_final, $asociada_contpaq, $empresas);
        return $cfdi;
    }

}

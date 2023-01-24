<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 04:18 PM
 */

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;

use App\Facades\Context;
use App\Informes\CFDEmpresaMes;
use App\Informes\CFDICompleto;
use App\Informes\Fiscal\InformeCostosCFDIvsCostosBalanza;
use App\Informes\Fiscal\InformeSATLP;
use App\Informes\Fiscal\PendientesREP;
use App\Informes\Fiscal\PendientesREPEmpresa;
use App\Informes\Fiscal\PendientesREPEmpresaProveedor;
use App\Informes\Fiscal\PendientesREPProveedorEmpresa;
use App\Models\CADECO\Obra;
use App\Models\SEGURIDAD_ERP\catCFDI\TipoComprobante;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Documentacion\CtgTipoTransaccion;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgNoLocalizado;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\Proyecto;
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

    public function obtenerInformeCostosCFDIvsCostosBalanza($data)
    {
        $informe["informe"] = InformeCostosCFDIvsCostosBalanza::get($data);
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

    public function obtenerListaCFDIMesAnio($data)
    {
        $cfdi = InformeCostosCFDIvsCostosBalanza::getListaCFDI($data);
        return $cfdi;
    }

    public function getListaCFDI($data)
    {
        if($data["tipo"] == 1){
            $cfdi = InformeSATLP::getListaCFDI($data);
        } else if($data["tipo"] == 2){
            $cfdi = InformeSATLP::getListaCFDIOmitidosDivisa($data);
        }
        else if($data["tipo"] == 3){
            $cfdi = InformeSATLP::getListaCFDIOmitidosReemplazo($data);
        }else if($data["tipo"] == 4){
            $cfdi = InformeSATLP::getListaCFDIOmitidosReemplazado($data);
        }else if($data["tipo"] == 5){
            $cfdi = InformeSATLP::getListaCFDIIngresos($data);
        }else if($data["tipo"] == 6){
            $cfdi = InformeSATLP::getListaCFDIEgresos($data);
        }else if($data["tipo"] == 7){
            $cfdi = InformeSATLP::getListaCFDIReconocidos($data);
        }else if($data["tipo"] == 8){
            $cfdi = InformeSATLP::getListaCFDINoReconocidos($data);
        }else if($data["tipo"] == 9){
            $cfdi = InformeSATLP::getListaCFDIARevisar($data);
        }else if($data["tipo"] == 10){
            $cfdi = InformeSATLP::getListaCFDIOmitidosDispersion($data);
        }else if($data["tipo"] == 11){
            $cfdi = InformeSATLP::getListaCFDIReemplazadosNoCancelados($data);
        }else if($data["tipo"] == 12){
            $cfdi = InformeSATLP::getListaCFDIReemplazados($data);
        }else if($data["tipo"] == 13){
            $cfdi = InformeSATLP::getListaCFDICancelados($data);
        }

        return $cfdi;
    }

    public function obtenerListaCFDICostosCFDIBalanza($data)
    {
        if($data["tipo"] == 9){
            $cfdi = InformeCostosCFDIvsCostosBalanza::getListaCFDIEjercicioPosterior($data);
        } else {
            $cfdi = InformeCostosCFDIvsCostosBalanza::getListaCFDI($data);
        }

        return $cfdi;
    }

    public function obtenerNumeroEmpresa()
    {
        $id_obra = Context::getIdObra();
        $base_datos = Context::getDatabase();

        $proyecto = Proyecto::query()->where('base_datos','=',Context::getDatabase())->first();

        $configuracion = ConfiguracionObra::where('id_proyecto','=',$proyecto->id)
            ->where('id_obra','=',$id_obra)->first();

        if($configuracion->numero_obra_contpaq)
        {
            return $configuracion->numero_obra_contpaq;
        }else {
            abort(500, "No se ha configurado el número de empresa contpaq para este proyecto en SAO. \n \n Por favor comuniquese con soporte a aplicaciones enviando un correo a la dirección: soporte_aplicaciones@desarrollo-hi.atlassian.net");
        }


    }

    public function getRFCObra()
    {
        $obra = Obra::find(Context::getIdObra());
        if($obra){
            return $obra->rfc;
        }
    }

    public function getInformeREP($data)
    {
        $informe["informe"] = PendientesREP::get($data);
        return $informe;
    }

    public function getInformeREPProveedorEmpresa($data)
    {
        $informe["informe"] = PendientesREPProveedorEmpresa::get($data);
        return $informe;
    }

    public function getInformeREPEmpresaProveedor($data)
    {
        $informe["informe"] = PendientesREPEmpresaProveedor::get($data);
        return $informe;
    }

    public function getInformeREPEmpresa($data)
    {
        $informe["informe"] = PendientesREPEmpresa::get($data);
        return $informe;
    }

}

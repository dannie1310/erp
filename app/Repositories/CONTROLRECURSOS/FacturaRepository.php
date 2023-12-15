<?php

namespace App\Repositories\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\CtgMoneda;
use App\Models\CONTROL_RECURSOS\Empresa;
use App\Models\CONTROL_RECURSOS\Factura;
use App\Models\CONTROL_RECURSOS\Proveedor;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\AvisoSATOmitir;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\DB;

class FacturaRepository extends Repository implements RepositoryInterface
{
    public function __construct(Factura $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getProveedor(Array $datos){
        $proveedor = Proveedor::whereRaw("REPLACE(RFC,'-','') = '".str_replace("-","",$datos["rfc"])."'")
            ->where('Estatus', 1)
            ->whereIn('TipoProveedor',[1,2,3])->first();
        $salida = null;
        if($proveedor){
            $salida =[
                "id"=>$proveedor->getKey(),
                "rfc"=>$proveedor->RFC,
                "razon_social"=>$proveedor->RazonSocial,
                "nuevo"=>0,
            ];
        }
        return $salida;
    }

    public function getEmpresa(Array $datos){
        $empresa = Empresa::where("RFC","=",$datos["rfc"])
            ->where('Estatus', 1)->first();
        $salida = null;

        if($empresa){
            $salida =[
                "id"=>$empresa->getKey(),
                "rfc"=>$empresa->RFC,
                "razon_social"=>$empresa->RazonSocial
            ];
        }
        return $salida;
    }

    public function getEFO($rfc)
    {
        $efo = DB::connection("seguridad")->table("Fiscal.ctg_efos")
            ->where("rfc","=",$rfc)
            ->first();
        return $efo;
    }

    public function getMoneda($moneda)
    {
        if($moneda == 'MXN')
        {
            $moneda = 'MXP';
        }
        $idmoneda = CtgMoneda::where("corto","=",$moneda)->first();
        return $idmoneda ? $idmoneda->getKey() : null;
    }

    public function getMonedas()
    {
        $monedas = CtgMoneda::all();
        $datos = [];
        foreach ($monedas as $moneda)
        {
            $datos[] = [
                "id_moneda"=>$moneda->getKey(),
                "moneda"=>$moneda->moneda,
                "corto"=>$moneda->corto
            ];
        }
        return $datos;
    }

    public function validaExistenciaRepositorio($uuid)
    {
        $factura_repositorio = FacturaRepositorio::whereNotNull("id_transaccion")
            ->where("uuid","=", $uuid)->first();
        return $factura_repositorio;
    }

    public function getBuscarProveedor($id)
    {
        return Proveedor::where('IdProveedor',$id)->first();
    }

    public function getEsOmitido($mensaje, $rfc_emisor, $uuid)
    {
        $explode = explode("-",$mensaje);
        $codigo = trim($explode[0]);
        $existe = AvisoSATOmitir::where("rfc_emisor",$rfc_emisor)
            ->where("clave",$codigo)
            ->where("estado",1)
            ->count();
        if($existe == 1){
            return $existe;
        } else {
            $existe = AvisoSATOmitir::where("uuid",$uuid)
                ->where("clave",$codigo)
                ->where("estado",1)
                ->count();
            return $existe;
        }
    }

    public function getArchivoSQL($archivo)
    {
        return DB::raw("CONVERT(VARBINARY(MAX), '" . $archivo . "')");
    }

    public function registrar($data)
    {
        return $this->model->registrar($data);
    }

    public function buscarDocumentoUuid($uuid)
    {
        return Factura::where('uuid', $uuid)->first();
    }

    public function getEmpresaSat($rfc)
    {
        return EmpresaSAT::where('rfc', $rfc)->first();
    }

    public function getProveedorSat($rfc)
    {
        return ProveedorSAT::where('rfc', $rfc)->first();
    }

    public function eliminar($id)
    {
        $factura = $this->show($id);
        $elimino = $factura->eliminar();
        $factura->desvinculaFacturaRepositorio();
        return $elimino;
    }

    public function buscarRepositorioFactura($uuid)
    {
        $factura_repositorio = FacturaRepositorio::where("uuid","=", $uuid)->first();
        return $factura_repositorio;
    }

    public function buscarFactura($uuid)
    {
        return $this->model->where('uuid', $uuid)->first();
    }
}

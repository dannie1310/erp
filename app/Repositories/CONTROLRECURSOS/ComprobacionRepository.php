<?php

namespace App\Repositories\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Comprobacion;
use App\Models\CONTROL_RECURSOS\CtgMoneda;
use App\Models\CONTROL_RECURSOS\DoctoComp;
use App\Models\CONTROL_RECURSOS\Empresa;
use App\Models\CONTROL_RECURSOS\Factura;
use App\Models\CONTROL_RECURSOS\Proveedor;
use App\Models\CONTROL_RECURSOS\RelacionGastoDocumento;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class ComprobacionRepository extends Repository implements RepositoryInterface
{
    public function __construct(Comprobacion $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrar($data)
    {
        return $this->model->registrar($data);
    }

    public function buscarRepositorioFactura($uuid)
    {
        $factura_repositorio = FacturaRepositorio::where("uuid","=", $uuid)->first();
        return $factura_repositorio;
    }

    public function getEFO($rfc)
    {
        return CtgEfos::where("rfc","=",$rfc)->first();
    }

    public function buscarDocumentoUuid($uuid)
    {
        return DoctoComp::where('uuid', $uuid)->first();
    }

    public function getProveedor(Array $datos){
        $proveedor = Proveedor::whereRaw("REPLACE(RFC,'-','') = '".str_replace("-","",$datos["rfc"])."'")
            ->porTipos(2)->porEstados(1)
            ->where('Estatus', 1)
            ->where('TipoProveedor', 2)->empleados()->first();
        $salida = null;
        if($proveedor){
            $salida =[
                "id"=>$proveedor->getKey(),
                "rfc"=>$proveedor->RFC,
                "razon_social"=>$proveedor->RazonSocial
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

    public function getMoneda($moneda)
    {
        if($moneda == 'MXN')
        {
            $moneda = 'MXP';
        }
        $idmoneda = CtgMoneda::where("corto","=",$moneda)->first();
        return $idmoneda ? $idmoneda->getKey() : null;
    }


    public function buscarPagoDocumentoUuid($uuid)
    {
        return Factura::where('uuid', $uuid)->first();
    }


    public function buscarRelacionDocumentoUuid($uuid)
    {
        return RelacionGastoDocumento::where('uuid', $uuid)->first();
    }

    public function getBuscarEmpresa($id)
    {
        return Empresa::where('IdEmpresa',$id)->first();
    }
}

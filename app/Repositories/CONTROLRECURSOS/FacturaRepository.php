<?php

namespace App\Repositories\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\CtgMoneda;
use App\Models\CONTROL_RECURSOS\Empresa;
use App\Models\CONTROL_RECURSOS\Factura;
use App\Models\CONTROL_RECURSOS\Proveedor;
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
        $proveedor = Proveedor::whereRaw("REPLACE(RFC,'-','') = '".str_replace("-","",$datos["rfc"])."'")->where('Estatus', 1)
            ->whereIn('TipoProveedor',[1,2])->first();
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
        $empresa = Empresa::where("RFC","=",$datos["rfc"])->first();
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
}

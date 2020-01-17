<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 13/01/2020
 * Time: 04:20 PM
 */

namespace App\Repositories\CADECO\Finanzas\Facturas;


use App\Models\CADECO\Empresa;
use App\Models\CADECO\Factura;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Repositories\RepositoryInterface;
USE Illuminate\Support\Facades\DB;
use App\Models\CADECO\Obra;
use App\Facades\Context;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(Factura $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getRFCObra()
    {
        $obra = Obra::find(Context::getIdObra());
        if($obra){
            return $obra->rfc;
        }
    }

    public function getEmpresa(Array $datos){
        $empresa = Empresa::where("rfc","=",$datos["rfc"])
            ->whereIn("tipo_empresa",[1,2,3,4])->first();

        if($empresa){
            $salida =[
                "id_empresa"=>$empresa->id_empresa,
                "rfc"=>$empresa->rfc,
                "razon_social"=>$empresa->razon_social,
                "nuevo"=>0,
            ];
        } else {
            $datos["tipo_empresa"] = 1;
            $empresa = Empresa::create($datos);
            $salida =[
                "id_empresa"=>$empresa->id_empresa,
                "rfc"=>$empresa->rfc,
                "razon_social"=>$empresa->razon_social,
                "nuevo"=>1,
            ];
        }
        return $salida;
    }

    public function getRFCEmpresa($id_empresa)
    {
        $empresa = Empresa::find($id_empresa);
        if ($empresa) {
            $rfc = preg_replace("/[^0-9a-zA-Z\s]+/", "", $empresa->rfc);
            $rfc = strtoupper($rfc);
            return $rfc;
        } else {
            return null;
        }
    }

    public function getEFO($rfc)
    {
        $efo = DB::connection("seguridad")->table("Finanzas.ctg_efos")
        ->where("rfc","=",$rfc)
        ->first();
        return $efo;
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }

    public function getArchivoSQL($archivo)
    {
        return DB::raw("CONVERT(VARBINARY(MAX), '" . $archivo . "')");
    }

    public function validaExistenciaRepositorio($hash_file, $uuid)
    {
        $factura_repositorio = FacturaRepositorio::where('hash_file', '=', $hash_file)
            ->orWhere("uuid","=", $uuid)->first();

        if($factura_repositorio){
            $factura_repositorio->load("usuario");
            abort(403, 'Archivo cargado previamente:
            Registró: '.$factura_repositorio->usuario->nombre_completo.'
            BD: '.$factura_repositorio->proyecto->base_datos.'
            Proyecto: '.$factura_repositorio->obra->nombre.'
            Factura: '.$factura_repositorio->factura->numero_folio.'
            Fecha: '.$factura_repositorio->fecha_hora_registro_format);
        }
    }
}
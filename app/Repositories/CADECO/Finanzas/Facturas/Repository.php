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
use App\Repositories\RepositoryInterface;
USE Illuminate\Support\Facades\DB;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(Factura $model)
    {
        parent::__construct($model);
        $this->model = $model;
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
}
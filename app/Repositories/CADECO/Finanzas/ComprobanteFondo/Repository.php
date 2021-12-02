<?php
/**
 * Created by PhpStorm.
 * User: DBenitezC
 * Date: 24/02/2021
 * Time: 08:20 PM
 */

namespace App\Repositories\CADECO\Finanzas\ComprobanteFondo;



use App\Facades\Context;
use App\Models\CADECO\ComprobanteFondo;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Fondo;
use App\Models\CADECO\Obra;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\DB;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(ComprobanteFondo $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function findFondo($descripcion)
    {
        $fondos = Fondo::where('descripcion', 'LIKE', '%'.$descripcion.'%')->pluck('id_fondo');
        return $fondos;
    }

    public function orderByFondo($campo, $ordena)
    {
        $fondos = Fondo::orderBy($campo, $ordena)->pluck('id_fondo');
        return $fondos;
    }

    public function registrar($data)
    {
        return $this->model->registrar($data);
    }

    public function getEFO($rfc)
    {
        $efo = DB::connection("seguridad")->table("Fiscal.ctg_efos")
            ->where("rfc","=",$rfc)
            ->first();
        return $efo;
    }

    public function getRFCObra()
    {
        $obra = Obra::find(Context::getIdObra());
        if($obra){
            return $obra->rfc;
        }
    }

    public function validaExistenciaRepositorio($uuid)
    {
        $factura_repositorio = FacturaRepositorio::whereNotNull("id_transaccion")
            ->where("uuid","=", $uuid)->first();
        return $factura_repositorio;
    }
}

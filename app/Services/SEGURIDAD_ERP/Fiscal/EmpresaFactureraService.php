<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 24/06/2020
 * Time: 02:09 PM
 */

namespace App\Services\SEGURIDAD_ERP\Fiscal;
use App\Models\SEGURIDAD_ERP\Fiscal\EmpresaFacturera as Model;
use App\Repositories\SEGURIDAD_ERP\Fiscal\EmpresaFactureraRepository as Repository;


class EmpresaFactureraService
{
    protected $repository;
    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function buscarCoincidencias($parametros)
    {
        $arr_empresas_factureras = $parametros->empresas_factureras;
        foreach($arr_empresas_factureras as $empresa_facturera){
            $facturera = $this->repository->show($empresa_facturera["id"]);
            $facturera->actualizaPalabrasClave($empresa_facturera["palabras_clave"]);
            $coincidencias[$empresa_facturera["razon_social"]] =$facturera->buscarCoincidencias($empresa_facturera["palabras_clave"]);
        }
        return [$coincidencias];
    }

}
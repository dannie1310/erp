<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 11/03/2020
 * Time: 03:55 PM
 */

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicion;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class SolicitudEdicionRepository extends Repository implements RepositoryInterface
{
    public function __construct(SolicitudEdicion $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrar(array $datos)
    {
        return $this->model->registrar($datos);
    }

    public function autorizar(array $data, $id)
    {
        $item = $this->show($id);
        $item->autorizar($data);
        return $item;
    }

    public function rechazar(array $datos)
    {
        return $this->model->rechazar($datos);
    }

    public function aplicar()
    {
        return $this->model->aplicar();
    }

    public function getListaBDContpaq(){
        return Empresa::where("Editable","1")->select("AliasBDD","IdContpaq")->get();
    }

}
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
use Illuminate\Support\Facades\DB;


class SolicitudEdicionRepository extends Repository implements RepositoryInterface
{
    public function __construct(SolicitudEdicion $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrar(array $datos){
        $model = $this->model->registrar($datos);
        $model->refresh();
        return $model;
    }

    public function autorizarPorPolizas(array $data, $id)
    {
        $item = $this->show($id);
        $item->autorizarPorPolizas($data);
        return $item;
    }

    public function autorizarPorPartidas(array $data, $id)
    {
        $item = $this->show($id);
        $item->autorizarPorPartidas($data);
        return $item;
    }

    public function rechazar($id)
    {
        return $this->show($id)->rechazar();
    }

    public function aplicar($id)
    {
        return $this->show($id)->aplicar();
    }

    public function getListaBDContpaq(){
        return Empresa::where("Editable","1")->select("AliasBDD","IdEmpresaContpaq")->get();
    }

    public function getPolizasSolicitud($id){
        return $this->show($id)->getPolizasSolicitud();
    }
}

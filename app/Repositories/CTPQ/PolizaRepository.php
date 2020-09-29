<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 17/02/2020
 * Time: 03:52 PM
 */

namespace App\Repositories\CTPQ;

use App\Models\CTPQ\Poliza;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class PolizaRepository extends Repository implements RepositoryInterface
{
    public function __construct(Poliza $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function update(array $datos, $id)
    {
        return $this->show($id)->actualiza($datos);
    }

    public function find(array $datos){
        return $this->model->where("Folio","=",$datos["folio"])
            ->where("Fecha","=",$datos["fecha"])
            ->where("TipoPol","=",$datos["tipo"])
            ->get();
    }

}

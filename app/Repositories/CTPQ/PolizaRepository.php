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
        $item = $this->show($id);
        $item->actualiza($datos);
        return $item;
    }

}
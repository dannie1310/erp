<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 17/02/2020
 * Time: 03:52 PM
 */

namespace App\Repositories\CADECO;

use App\Models\CADECO\Estimacion;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class EstimacionRepository extends Repository implements RepositoryInterface
{
    public function __construct(Estimacion $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }

    public function update(array $data, $id)
    {
        return $this->show($id)->editar($data);
    }

    public function descargaLayout($id)
    {
        return $this->model->descargaLayout($id);
    }

    public function descargaLayoutEdicion($id)
    {
        return $this->model->descargaLayoutEdicion($id);
    }
}

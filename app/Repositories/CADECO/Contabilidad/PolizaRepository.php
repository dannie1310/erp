<?php


namespace App\Repositories\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\Poliza;
use App\Repositories\RepositoryInterface;

class PolizaRepository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(Poliza $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function asociarCFDI($data)
    {
        return $this->model->asociarCFDI($data);
    }

    public function getAsociarCFDI()
    {
        return $this->model->buscarPolizasSinAsociarCFDI();
    }

    public function getCFDIPorCargar()
    {
        return $this->model->buscarCFDISinCargarAlADD();
    }
}

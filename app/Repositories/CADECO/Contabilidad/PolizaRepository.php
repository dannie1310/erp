<?php

namespace App\Repositories\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\EstatusPrepoliza;
use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\CADECO\Contabilidad\TipoPolizaContpaq;
use App\Traits\RepositoryTrait;

class PolizaRepository
{
    use RepositoryTrait;

    /**
     * @var Poliza
     */
    private $model;

    /**
     * PolizaRepository constructor.
     * @param Poliza $model
     */
    public function __construct(Poliza $model)
    {
        $this->model = $model;
    }

    public function getEstatus() {
        return EstatusPrepoliza::all();
    }
    public function getTiposPolizaContpaq() {
        return  TipoPolizaContpaq::all();
    }

    public function where($where) {
        $this->model = $this->model->where($where);
        return $this;
    }
}
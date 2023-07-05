<?php

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoPartida;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class LayoutPasivoPartidaRepository extends Repository implements RepositoryInterface
{
    public function __construct(LayoutPasivoPartida $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
}

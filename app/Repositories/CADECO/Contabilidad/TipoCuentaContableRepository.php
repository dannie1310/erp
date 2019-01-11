<?php

namespace App\Repositories\CADECO\Contabilidad;



use App\Models\CADECO\Contabilidad\TipoCuentaContable;
use App\Traits\RepositoryTrait;

class TipoCuentaContableRepository
{
    use RepositoryTrait;

    /**
     * @var TipoCuentaContable
     */
    protected $model;

    /**
     * TipoCuentaContableRepository constructor.
     * @param TipoCuentaContable $model
     */
    public function __construct(TipoCuentaContable $model)
    {
        $this->model = $model;
    }
}
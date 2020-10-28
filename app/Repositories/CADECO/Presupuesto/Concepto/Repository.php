<?php

namespace App\Repositories\CADECO\Presupuesto\Concepto;

use App\Models\CADECO\Concepto;
use App\Repositories\RepositoryInterface;
use App\Scopes\ActivoScope;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param Requisicion $model
     */
    public function __construct(Concepto $model)
    {
        $this->model = $model;
    }

    public function list($nivel_padre)
    {
        return $this->model->withoutGlobalScope(ActivoScope::class)
            ->whereRaw("substring(conceptos.nivel,1,len(conceptos.nivel)-4) = ?", [$nivel_padre])
            ->whereRaw("(len(conceptos.nivel)-4) >= 0", [])
            ->orderBy("nivel")
            ->get();
    }
}

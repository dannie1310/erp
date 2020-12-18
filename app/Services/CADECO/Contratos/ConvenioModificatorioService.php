<?php


namespace App\Services\CADECO\Contratos;

use App\Models\CADECO\SubcontratosCM\Transaccion as Model;
use App\Repositories\CADECO\SubcontratosCM\TransaccionRepository as Repository;

class ConvenioModificatorioService
{
    protected $repository;

    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
    }

}

<?php


namespace App\Services\SEGURIDAD_ERP\Compras;

use App\Models\SEGURIDAD_ERP\Compras\CtgAreaCompradora;
use App\Repositories\Repository;

class  CtgAreaCompradoraService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CtgAreaCompradoraService constructor
     * @param CtgAreaCompradora $model
     */

    public function __construct(CtgAreaCompradora $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {

        return $this->repository->all($data);
    }
}

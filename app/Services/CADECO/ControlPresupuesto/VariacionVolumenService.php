<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 09:45 PM
 */

namespace App\Services\CADECO\ControlPresupuesto;

use App\Repositories\Repository;
use App\Models\CADECO\ControlPresupuesto\VariacionVolumen;

class VariacionVolumenService{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * VariacionVolumen constructor.
     *
     * @param VariacionVolumen $model
     */
    public function __construct(VariacionVolumen $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }
}
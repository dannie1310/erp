<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 07/03/2019
 * Time: 11:47 AM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Subcontrato;
use App\Repositories\Repository;

class SubcontratoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SubcontratoService constructor.
     * @param Subcontrato $model
     */
    public function __construct(Subcontrato $model)
    {
        $this->repository = new Repository($model);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }
}
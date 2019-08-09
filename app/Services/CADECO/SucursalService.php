<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 08/08/19
 * Time: 06:00 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Sucursal;
use App\Repositories\Repository;

class SucursalService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SucursalService constructor
     *
     * @param Sucursal $model
     */

    public function __construct(Sucursal $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data){
        return $this->repository->paginate();
    }

    public function show($id){
        return $this->repository->show($id);
    }
//    public function store(array $data)
//    {
//
//    }

}

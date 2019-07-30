<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 23/07/2019
 * Time: 07:22 PM
 */
namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\CtgTipoFondo;
use App\Repositories\Repository;

class CtgTipoFondoService{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CtgTipoFondoService constructor.
     * @param CtgTipoFondo $model
     */

    public function  __construct(CtgTipoFondo $model){
        $this->repository = new Repository($model);
    }

    public function index($data){
        return $this->repository->all($data);
    }
    public function show($id)
    {
        return $this->repository->show($id);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 06/08/2019
 * Time: 12:00 PM
 */


namespace App\Services\CADECO;


use App\Models\CADECO\Unidad;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class UnidadService
{

    /**
     * @var Repository
     */
    protected $repository;


    /**
     * UnidadService constructor
     * @param Unidad $model
     */

    public function __construct(Unidad $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
    public function show($id)
    {
        return $this->repository->show($id);
    }

}

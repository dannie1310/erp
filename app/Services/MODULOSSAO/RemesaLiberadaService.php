<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 27/05/2019
 * Time: 06:16 PM
 */

namespace App\Services\MODULOSSAO;


use App\Models\MODULOSSAO\ControlRemesas\RemesaLiberada;
use App\Repositories\Repository;

class RemesaLiberadaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * RemesaLiberadaService constructor.
     * @param RemesaLiberada $model
     */
    public function __construct(RemesaLiberada $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
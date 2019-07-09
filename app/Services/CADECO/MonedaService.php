<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/03/19
 * Time: 05:38 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Moneda;
use App\Repositories\Repository;

class MonedaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * MonedaService constructor.
     * @param Moneda $model
     */
    public function __construct(Moneda $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}

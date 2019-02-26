<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 14/02/19
 * Time: 12:59 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Fondo;
use App\Repositories\Repository;

class FondoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * FondoService constructor.
     *
     * @param Fondo $model
     */
    public function __construct(Fondo $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
<?php

namespace App\Services\IGH;

use App\Models\IGH\Menu;
use App\Models\SEGURIDAD_ERP\Sistema;
use App\Repositories\Repository;

class MenuService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * Menu constructor.
     * @param Menu $model
     */
    public function __construct(Menu $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        $sistema = new Sistema();
        $aplicaciones = $sistema->aplicaciones()->get();
        $menu = $this->repository->all($data);
        return $aplicaciones->merge($menu);
    }
}
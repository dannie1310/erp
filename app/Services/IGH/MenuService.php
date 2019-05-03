<?php

namespace App\Services\IGH;

use App\Models\IGH\Menu;
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
        return $this->repository->all($data);
    }
}
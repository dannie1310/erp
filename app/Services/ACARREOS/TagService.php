<?php


namespace App\Services\ACARREOS;


class TagService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ViajeNetoService constructor.
     * @param ViajeNeto $model
     */
    public function __construct(ViajeNeto $model)
    {
        $this->repository = new Repository($model);
    }

}

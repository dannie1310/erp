<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 30/01/19
 * Time: 07:45 PM
 */

namespace App\Services\CADECO;


use App\Repositories\CADECO\CuentaRepository;

class CuentaService
{
    /**
     * @var CuentaRepository
     */
    protected $repository;

    /**
     * CuentaService constructor.
     * @param CuentaRepository $repository
     */
    public function __construct(CuentaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
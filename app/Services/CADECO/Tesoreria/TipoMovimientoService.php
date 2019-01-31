<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 30/01/19
 * Time: 07:23 PM
 */

namespace App\Services\CADECO\Tesoreria;


use App\Repositories\CADECO\Tesoreria\TipoMovimientoRepository;

class TipoMovimientoService
{
    /**
     * @var TipoMovimientoRepository
     */
    protected $repository;

    /**
     * TipoMovimientoService constructor.
     * @param TipoMovimientoRepository $repository
     */
    public function __construct(TipoMovimientoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return $this->repository->all();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 06:12 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Repositories\CADECO\Contabilidad\EstatusPrepolizaRepository;

class EstatusPrepolizaService
{
    /**
     * @var EstatusPrepolizaRepository
     */
    protected $repository;

    /**
     * EstatusPrepolizaService constructor.
     * @param EstatusPrepolizaRepository $repository
     */
    public function __construct(EstatusPrepolizaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index() {
        return $this->repository->all();
    }
}
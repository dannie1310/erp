<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 06:23 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Repositories\CADECO\Contabilidad\TipoPolizaContpaqRepository;

class TipoPolizaContpaqService
{
    /**
     * @var TipoPolizaContpaqRepository
     */
    protected $repository;

    /**
     * TipoPolizaContpaqService constructor.
     * @param TipoPolizaContpaqRepository $repository
     */
    public function __construct(TipoPolizaContpaqRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index() {
        return $this->repository->all();
    }
}
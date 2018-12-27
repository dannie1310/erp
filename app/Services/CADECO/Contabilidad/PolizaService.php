<?php

namespace App\Services\CADECO\Contabilidad;


use App\Repositories\CADECO\Contabilidad\PolizaRepository;

class PolizaService
{
    /**
     * @var PolizaRepository
     */
    protected $poliza;

    /**
     * PolizaService constructor.
     * @param PolizaRepository $poliza
     */
    public function __construct(PolizaRepository $poliza)
    {
        $this->poliza = $poliza;
    }

    public function index($data) {
        return $this->poliza->all($data);
    }
}
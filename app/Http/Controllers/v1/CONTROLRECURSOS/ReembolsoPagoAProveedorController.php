<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\ReembolsoPagoAProveedorTransformer;
use App\Services\CONTROLRECURSOS\ReembolsoPagoAProveedorService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ReembolsoPagoAProveedorController extends Controller
{
    use ControllerTrait;

    /**
     * @var ReembolsoPagoAProveedorService
     */
    protected $service;

    /**
     * @var ReembolsoPagoAProveedorTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param ReembolsoPagoAProveedorService $service
     * @param ReembolsoPagoAProveedorTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(ReembolsoPagoAProveedorService $service, ReembolsoPagoAProveedorTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

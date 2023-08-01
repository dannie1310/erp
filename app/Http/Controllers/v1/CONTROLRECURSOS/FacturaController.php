<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\FacturaTransformer;
use App\Services\CONTROLRECURSOS\FacturaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class FacturaController extends Controller
{
    use ControllerTrait;

    /**
     * @var FacturaService
     */
    protected $service;

    /**
     * @var FacturaTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param FacturaService $service
     * @param FacturaTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(FacturaService $service, FacturaTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');
        //$this->middleware('permisoGlobal:autorizar_rechazar_transaccion_proveedor_no_localizado')->only(['autorizar','rechazar']);

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

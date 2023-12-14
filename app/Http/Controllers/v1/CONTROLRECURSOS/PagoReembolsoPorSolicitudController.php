<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\PagoReembolsoPorSolicitudTransformer;
use App\Services\CONTROLRECURSOS\PagoReembolsoPorSolicitudService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class PagoReembolsoPorSolicitudController extends Controller
{
    use ControllerTrait;

    /**
     * @var PagoReembolsoPorSolicitudService
     */
    protected $service;

    /**
     * @var PagoReembolsoPorSolicitudTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    public function __construct(PagoReembolsoPorSolicitudService $service, Manager $fractal, PagoReembolsoPorSolicitudTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}

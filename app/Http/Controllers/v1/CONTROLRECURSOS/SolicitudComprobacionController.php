<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\SolicitudComprobacionTransformer;
use App\Services\CONTROLRECURSOS\SolicitudComprobacionService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SolicitudComprobacionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SolicitudComprobacionService
     */
    protected $service;

    /**
     * @var SolicitudComprobacionTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param SolicitudComprobacionService $service
     * @param SolicitudComprobacionTransformer $transformer
     */
    public function __construct(Manager $fractal, SolicitudComprobacionService $service, SolicitudComprobacionTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('permisoGlobal:registrar_reembolso_por_caja_chica')->only(['store']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

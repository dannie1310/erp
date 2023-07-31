<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\SolicitudPagoOCTransformer;
use App\Services\CONTROLRECURSOS\SolicitudPagoOCService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SolicitudPagoOCController extends Controller
{
    use ControllerTrait;

    /**
     * @var SolicitudPagoOCService
     */
    protected $service;

    /**
     * @var SolicitudPagoOCTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param SolicitudPagoOCService $service
     * @param SolicitudPagoOCTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(SolicitudPagoOCService $service, SolicitudPagoOCTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');
        //$this->middleware('permisoGlobal:autorizar_rechazar_transaccion_proveedor_no_localizado')->only(['autorizar','rechazar']);

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

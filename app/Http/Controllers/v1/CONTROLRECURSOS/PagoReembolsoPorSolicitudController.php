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

        $this->middleware('permisoGlobal:consultar_solicitud_pago_reembolso')->only(['show','paginate','index']);
        $this->middleware('permisoGlobal:registrar_solicitud_pago_reembolso')->only(['store']);
        //$this->middleware('permisoGlobal:editar_solicitud_pago_reembolso')->only(['update']);
        $this->middleware('permisoGlobal:eliminar_solicitud_pago_reembolso')->only(['destroy']);

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}

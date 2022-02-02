<?php
namespace App\Http\Controllers\v1\SEGURIDAD_ERP\IndicadoresFinanzas;

use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicadaTransformer;
use App\Services\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicadaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;


class SolicitudPagoAplicadaController extends Controller
{
    use ControllerTrait;

    public function __construct(SolicitudPagoAplicadaService $service, Manager $fractal, SolicitudPagoAplicadaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('permisoGlobal:acceso-tablero-control-finanzas-general');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function getIndicadorAplicadas()
    {
        //$this->service->procesaSolicitudesPagoParaIndicador();
        return $this->service->getIndicadorAplicadas();
    }

}

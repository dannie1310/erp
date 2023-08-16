<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\SolicitudRecursoTransformer;
use App\Services\CONTROLRECURSOS\SolicitudRecursoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SolicitudRecursoController extends Controller
{
    use ControllerTrait;

    /**
     * @var SolicitudRecursoService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SolicitudRecursoTransformer
     */
    protected $transformer;

    /**
     * @param SolicitudRecursoService $service
     * @param Manager $fractal
     * @param SolicitudRecursoTransformer $transformer
     */
    public function __construct(SolicitudRecursoService $service, Manager $fractal, SolicitudRecursoTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function descargaLayout($id)
    {
        return $this->service->layout($id);
    }

}

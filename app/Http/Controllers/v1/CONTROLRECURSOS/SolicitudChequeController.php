<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\SolicitudChequeTransformer;
use App\Services\CONTROLRECURSOS\SolicitudChequeService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SolicitudChequeController extends Controller
{
    use ControllerTrait;

    /**
     * @var SolicitudChequeService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SolicitudChequeTransformer
     */
    protected $transformer;

    /**
     * @param SolicitudChequeService $service
     * @param Manager $fractal
     * @param SolicitudChequeTransformer $transformer
     */
    public function __construct(SolicitudChequeService $service, Manager $fractal, SolicitudChequeTransformer $transformer)
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

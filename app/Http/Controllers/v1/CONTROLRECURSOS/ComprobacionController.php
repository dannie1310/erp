<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\ComprobacionTransformer;
use App\Services\CONTROLRECURSOS\ComprobacionService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ComprobacionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ComprobacionService
     */
    protected $service;

    /**
     * @var ComprobacionTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param ComprobacionService $service
     * @param ComprobacionTransformer $transformer
     */
    public function __construct(Manager $fractal, ComprobacionService $service, ComprobacionTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('permisoGlobal:registrar_reembolso_por_caja_chica')->only(['store']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

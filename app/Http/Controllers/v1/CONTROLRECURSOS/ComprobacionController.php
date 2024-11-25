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
     * @var ComprobacionTransformer
     */
    protected $transformer;

    /**
     * @var ComprobacionService
     */
    protected $service;

    /**
     * @param Manager $fractal
     * @param ComprobacionTransformer $transformer
     * @param ComprobacionService $service
     */
    public function __construct(Manager $fractal, ComprobacionTransformer $transformer, ComprobacionService $service)
    {
        $this->middleware('auth:api');

        $this->middleware('permisoGlobal:registrar_documento_recursos')->only(['store']);

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

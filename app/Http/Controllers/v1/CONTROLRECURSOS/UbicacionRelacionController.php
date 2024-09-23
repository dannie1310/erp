<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\UbicacionRelacionTransformer;
use App\Services\CONTROLRECURSOS\UbicacionRelacionService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class UbicacionRelacionController extends Controller
{
    use ControllerTrait;

    /**
     * @var UbicacionRelacionService
     */
    protected $service;

    /**
     * @var UbicacionRelacionTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param UbicacionRelacionService $service
     * @param UbicacionRelacionTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(UbicacionRelacionService $service, UbicacionRelacionTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

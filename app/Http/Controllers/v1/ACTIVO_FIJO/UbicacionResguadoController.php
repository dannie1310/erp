<?php

namespace App\Http\Controllers\v1\ACTIVO_FIJO;

use App\Http\Controllers\Controller;
use App\Http\Transformers\ACTIVO_FIJO\UbicacionResguadoTransformer;
use App\Services\ACTIVO_FIJO\UbicacionResguadoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class UbicacionResguadoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var UbicacionResguadoService
     */
    protected $service;

    /**
     * @var UbicacionResguadoTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param UbicacionResguadoService $service
     * @param UbicacionResguadoTransformer $transformer
     */
    public function __construct(Manager $fractal, UbicacionResguadoService $service, UbicacionResguadoTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\ProveedorTransformer;
use App\Services\CONTROLRECURSOS\ProveedorService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ProveedorController extends Controller
{
    use ControllerTrait;

    /**
     * @var ProveedorService
     */
    protected $service;

    /**
     * @var ProveedorTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param ProveedorService $service
     * @param ProveedorTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(ProveedorService $service, ProveedorTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\MonedaTransformer;
use App\Services\CONTROLRECURSOS\MonedaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class MonedaController extends Controller
{
    use ControllerTrait;

    /**
     * @var MonedaTransformer
     */
    protected $transformer;

    /**
     * @var MonedaService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param MonedaTransformer $transformer
     * @param MonedaService $service
     * @param Manager $fractal
     */
    public function __construct(MonedaTransformer $transformer, MonedaService $service, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->transformer = $transformer;
        $this->service = $service;
        $this->fractal = $fractal;
    }
}

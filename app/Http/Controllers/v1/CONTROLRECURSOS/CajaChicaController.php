<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\CajaChicaTransformer;
use App\Services\CONTROLRECURSOS\CajaChicaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CajaChicaController extends Controller
{
    use ControllerTrait;

    /**
     * @var CajaChicaService
     */
    protected $service;

    /**
     * @var CajaChicaTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param CajaChicaService $service
     * @param CajaChicaTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(CajaChicaService $service, CajaChicaTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

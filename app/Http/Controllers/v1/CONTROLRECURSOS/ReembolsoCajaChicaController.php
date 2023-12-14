<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\ReembolsoCajaChicaTransformer;
use App\Services\CONTROLRECURSOS\ReembolsoCajaChicaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ReembolsoCajaChicaController extends Controller
{
    use ControllerTrait;

    /**
     * @var ReembolsoCajaChicaService
     */
    protected $service;

    /**
     * @var ReembolsoCajaChicaTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param ReembolsoCajaChicaService $service
     * @param ReembolsoCajaChicaTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(ReembolsoCajaChicaService $service, ReembolsoCajaChicaTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

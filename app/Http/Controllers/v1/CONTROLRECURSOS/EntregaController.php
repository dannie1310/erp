<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\EntregaTransformer;
use App\Services\CONTROLRECURSOS\EntregaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class EntregaController extends Controller
{
    use ControllerTrait;

    /**
     * @var EntregaService
     */
    protected $service;

    /**
     * @var EntregaTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param EntregaService $service
     * @param EntregaTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(EntregaService $service, EntregaTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

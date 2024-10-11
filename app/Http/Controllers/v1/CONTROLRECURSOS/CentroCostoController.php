<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\CentroCostoTransformer;
use App\Services\CONTROLRECURSOS\CentroCostoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CentroCostoController extends Controller
{
    use ControllerTrait;

    /**
     * @var CentroCostoService
     */
    protected $service;

    /**
     * @var CentroCostoTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param CentroCostoService $service
     * @param CentroCostoTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(CentroCostoService $service, CentroCostoTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

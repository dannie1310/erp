<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Traits\ControllerTrait;
use App\Http\Transformers\CONTROLRECURSOS\TipoGastoTransformer;
use App\Services\CONTROLRECURSOS\TipoGastoService;
use League\Fractal\Manager;

class TipoGastoController extends Controller
{
    use ControllerTrait;

    /**
     * TipoGastoCompService
     */
    protected $service;

    /**
     * TipoGastoCompTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param $service
     * @param $transformer
     * @param Manager $fractal
     */
    public function __construct(TipoGastoService $service, TipoGastoTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

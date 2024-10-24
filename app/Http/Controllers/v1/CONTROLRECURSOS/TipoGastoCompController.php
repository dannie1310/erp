<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Traits\ControllerTrait;
use App\Http\Transformers\CONTROLRECURSOS\TipoGastoCompTransformer;
use App\Services\CONTROLRECURSOS\TipoGastoCompService;
use League\Fractal\Manager;

class TipoGastoCompController extends Controller
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
    public function __construct(TipoGastoCompService $service, TipoGastoCompTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

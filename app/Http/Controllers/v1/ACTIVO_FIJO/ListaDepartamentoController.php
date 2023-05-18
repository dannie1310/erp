<?php

namespace App\Http\Controllers\v1\ACTIVO_FIJO;

use App\Http\Controllers\Controller;
use App\Http\Transformers\ACTIVO_FIJO\ListaDepartamentoTransformer;
use App\Services\ACTIVO_FIJO\ListaDepartamentoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ListaDepartamentoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ListaDepartamentoService
     */
    protected $service;

    /**
     * @var ListaDepartamentoTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param ListaDepartamentoService $service
     * @param ListaDepartamentoTransformer $transformer
     */
    public function __construct(Manager $fractal, ListaDepartamentoService $service, ListaDepartamentoTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

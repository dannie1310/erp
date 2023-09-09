<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\SolRecSemanaAnioTransformer;
use App\Services\CONTROLRECURSOS\SolRecSemanaAnioService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SolRecSemanaAnioController extends Controller
{
    use ControllerTrait;

    /**
     * @var SolRecSemanaAnioService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SolRecSemanaAnioTransformer
     */
    protected $transformer;

    /**
     * @param SolRecSemanaAnioService $service
     * @param Manager $fractal
     * @param SolRecSemanaAnioTransformer $transformer
     */
    public function __construct(SolRecSemanaAnioService $service, Manager $fractal, SolRecSemanaAnioTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}

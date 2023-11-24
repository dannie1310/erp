<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\FirmaFirmanteTransformer;
use App\Services\CONTROLRECURSOS\FirmaFirmanteService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class FirmaFirmanteController extends Controller
{
    use ControllerTrait;

    /**
     * @var FirmaFirmanteService
     */
    protected $service;

    /**
     * @var FirmaFirmanteTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param FirmaFirmanteService $service
     * @param FirmaFirmanteTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(FirmaFirmanteService $service, FirmaFirmanteTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

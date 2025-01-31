<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\TasaIvaTransformer;
use App\Services\CONTROLRECURSOS\TasaIvaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TasaIvaController extends Controller
{
    use ControllerTrait;

    /**
     * @var TasaIvaService
     */
    protected $service;

    /**
     * @var TasaIvaTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param TasaIvaService $service
     * @param TasaIvaTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(TasaIvaService $service, TasaIvaTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

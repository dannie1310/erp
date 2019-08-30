<?php


namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\DatosEstimacionesTransformer;
use App\Services\CADECO\Finanzas\DatosEstimacionesService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class DatosEstimacionesController extends Controller
{
    use ControllerTrait;

    /**
     * @var DatosEstimacionesService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var DatosEstimacionesTransformer
     */
    private $transformer;

    /**
     * DatosEstimacionesController constructor.
     * @param DatosEstimacionesService $service
     * @param Manager $fractal
     * @param DatosEstimacionesTransformer $transformer
     */
    public function __construct(DatosEstimacionesService $service, Manager $fractal, DatosEstimacionesTransformer $transformer){
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

}
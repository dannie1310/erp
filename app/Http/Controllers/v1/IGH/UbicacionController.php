<?php

namespace App\Http\Controllers\v1\IGH;


use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\IGH\UbicacionService;
use App\Http\Transformers\IGH\UbicacionTransformer;

class UbicacionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var UbicacionService
     */
    protected $service;

    /**
     * @var UbicacionTransformer
     */
    protected $transformer;

    /**
     * UbicacionController constructor.
     *
     * @param Manager $fractal
     * @param UbicacionService $service
     * @param UbicacionTransformer $transformer
     */
    public function __construct(Manager $fractal, UbicacionService $service, UbicacionTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

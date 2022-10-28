<?php


namespace App\Http\Controllers\v1\IGH;


use App\Http\Controllers\Controller;
use App\Http\Transformers\IGH\TipoCambioTransformer;
use App\Services\IGH\TipoCambioService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TipoCambioController extends Controller
{
    use ControllerTrait;

    /**
     * @var TipoCambioTransformer
     */
    protected $transformer;

    /**
     * @var TipoCambioService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * TipoCambioController constructor.
     * @param TipoCambioTransformer $transformer
     * @param TipoCambioService $service
     * @param Manager $fractal
     */
    public function __construct(TipoCambioTransformer $transformer, TipoCambioService $service, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->transformer = $transformer;
        $this->service = $service;
        $this->fractal = $fractal;
    }
}

<?php


namespace App\Http\Controllers\v1\SEGUIMIENTO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGUIMIENTO\MonedaTransformer;
use App\Services\SEGUIMIENTO\Finanzas\MonedaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class MonedaController extends Controller
{
    use ControllerTrait;

    /**
     * @var MonedaService
     */
    protected $service;

    /**
     * @var MonedaTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * MonedaController constructor.
     * @param MonedaService $service
     * @param MonedaTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(MonedaService $service, MonedaTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

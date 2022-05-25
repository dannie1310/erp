<?php


namespace App\Http\Controllers\v1\SEGUIMIENTO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGUIMIENTO\Finanzas\FacturaTransformer;
use App\Services\SEGUIMIENTO\Finanzas\FacturaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class FacturaController extends Controller
{
    use ControllerTrait;

    /**
     * @var FacturaService
     */
    protected $service;

    /**
     * @var FacturaTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * FacturaController constructor.
     * @param FacturaService $service
     * @param FacturaTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(FacturaService $service, FacturaTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

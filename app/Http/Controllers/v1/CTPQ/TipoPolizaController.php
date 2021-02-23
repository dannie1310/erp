<?php


namespace App\Http\Controllers\v1\CTPQ;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CTPQ\TipoPolizaTransformer;
use App\Services\CTPQ\TipoPolizaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TipoPolizaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TipoPolizaService
     */
    protected $service;

    /**
     * @var TipoPolizaTransformer
     */
    protected $transformer;

    /**
     * TipoPolizaController constructor.
     * @param Manager $fractal
     * @param TipoPolizaService $service
     * @param TipoPolizaTransformer $transformer
     */
    public function __construct(Manager $fractal, TipoPolizaService $service, TipoPolizaTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

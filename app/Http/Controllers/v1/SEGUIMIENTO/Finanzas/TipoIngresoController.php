<?php


namespace App\Http\Controllers\v1\SEGUIMIENTO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGUIMIENTO\TipoIngresoTransformer;
use App\Services\SEGUIMIENTO\Finanzas\TipoIngresoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TipoIngresoController extends Controller
{
    use ControllerTrait;

    /**
     * @var TipoIngresoService
     */
    protected $service;

    /**
     * @var TipoIngresoTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * TipoIngresoController constructor.
     * @param TipoIngresoService $service
     * @param TipoIngresoTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(TipoIngresoService $service, TipoIngresoTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

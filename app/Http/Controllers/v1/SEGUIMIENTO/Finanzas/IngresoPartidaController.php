<?php


namespace App\Http\Controllers\v1\SEGUIMIENTO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGUIMIENTO\IngresoPartidaTransformer;
use App\Services\SEGUIMIENTO\Finanzas\IngresoPartidaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class IngresoPartidaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var IngresoPartidaService
     */
    protected $service;

    /**
     * @var IngresoPartidaTransformer
     */
    protected $transformer;

    /**
     * IngresoPartidaController constructor.
     * @param Manager $fractal
     * @param IngresoPartidaService $service
     * @param IngresoPartidaTransformer $transformer
     */
    public function __construct(Manager $fractal, IngresoPartidaService $service, IngresoPartidaTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

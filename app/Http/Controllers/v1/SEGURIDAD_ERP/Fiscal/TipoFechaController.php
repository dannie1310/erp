<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Fiscal\TipoFechaTransformer;
use App\Services\SEGURIDAD_ERP\Fiscal\TipoFechaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TipoFechaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TipoFechaService
     */
    protected $service;

    /**
     * @var TipoFechaTransformer
     */
    protected $transformer;

    /**
     * TipoFechaController constructor.
     * @param Manager $fractal
     * @param TipoFechaService $service
     * @param TipoFechaTransformer $transformer
     */
    public function __construct(Manager $fractal, TipoFechaService $service, TipoFechaTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

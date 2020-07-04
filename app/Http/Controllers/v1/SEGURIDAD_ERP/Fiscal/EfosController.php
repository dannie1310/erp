<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Fiscal\EfosTransformer;
use App\Services\SEGURIDAD_ERP\Fiscal\EfosService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class EfosController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var EfosService
     */
    protected $service;

    /**
     * @var EfosTransformer
     */
    protected $transformer;

    /**
     * EfoController constructor.
     * @param Manager $fractal
     * @param EfosService $service
     * @param EfosTransformer $transformer
     */
    public function __construct(Manager $fractal, EfosService $service, EfosTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

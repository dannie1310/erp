<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Fiscal\NoDeducidoTransformer;
use App\Services\SEGURIDAD_ERP\Fiscal\NoDeducidoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class NoDeducidoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var NoDeducidoService
     */
    protected $service;

    /**
     * @var NoDeducidoTransformer
     */
    protected $transformer;

    /**
     * NoDeducidoController constructor.
     * @param Manager $fractal
     * @param NoDeducidoService $service
     * @param NoDeducidoTransformer $transformer
     */
    public function __construct(Manager $fractal, NoDeducidoService $service, NoDeducidoTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

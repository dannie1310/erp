<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal;


use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\SEGURIDAD_ERP\Fiscal\NoLocalizadoService;
use App\Http\Transformers\SEGURIDAD_ERP\Fiscal\NoLocalizadoTransformer;

class NoLocalizadoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var NoLocalizadoService
     */
    protected $service;

    /**
     * @var NoLocalizadoTransformer
     */
    protected $transformer;

    /**
     * NoDeducidoController constructor.
     * @param Manager $fractal
     * @param NoLocalizadoService $service
     * @param NoLocalizadoTransformer $transformer
     */
    public function __construct(Manager $fractal, NoLocalizadoService $service, NoLocalizadoTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

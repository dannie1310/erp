<?php


namespace App\Http\Controllers\v1\SEGUIMIENTO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGUIMIENTO\ClienteTransformer;
use App\Services\SEGUIMIENTO\Finanzas\ClienteService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ClienteController extends Controller
{
    use ControllerTrait;

    /**
     * @var ClienteService
     */
    protected $service;

    /**
     * @var ClienteTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * ClienteController constructor.
     * @param ClienteService $service
     * @param ClienteTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(ClienteService $service, ClienteTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

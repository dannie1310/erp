<?php


namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\UnidadTransformer;
use App\Services\CADECO\UnidadService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class UnidadController extends Controller
{
    use ControllerTrait;

    /**
     * @var UnidadService
     */
    protected $service;

    /**
     * @var UnidadTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * UnidadController constructor.
     * @param UnidadService $service
     * @param UnidadTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(UnidadService $service, UnidadTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

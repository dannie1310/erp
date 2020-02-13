<?php


namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\ContratoTransformer;
use App\Services\CADECO\ContratoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ContratoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ContratoService
     */
    protected $service;

    /**
     * @var ContratoTransformer
     */
    protected $transformer;

    /**
     * ContratoController constructor.
     * @param Manager $fractal
     * @param ContratoService $service
     * @param ContratoTransformer $transformer
     */
    public function __construct(Manager $fractal, ContratoService $service, ContratoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

<?php


namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Compras\InventarioTransformer;
use App\Services\CADECO\InventarioService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class InventarioController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var InventarioService
     */
    protected $service;

    /**
     * @var InventarioTransformer
     */
    protected $transformer;

    /**
     * AlmacenController constructor.
     *
     * @param Manager $fractal
     * @param InventarioService $service
     * @param InventarioTransformer $transformer
     */
    public function __construct(Manager $fractal, InventarioService $service, InventarioTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}
<?php


namespace App\Http\Controllers\v1\CADECO\Almacenes;


use App\Http\Controllers\Controller;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class InventarioFisicoController extends Controller
{
    use ControllerTrait;

    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    protected $transformer;

    public function __construct(AjustePositivoService $service, Manager $fractal, AjustePositivoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

}
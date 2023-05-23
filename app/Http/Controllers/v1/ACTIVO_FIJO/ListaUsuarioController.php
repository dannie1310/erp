<?php

namespace App\Http\Controllers\v1\ACTIVO_FIJO;

use App\Http\Controllers\Controller;
use App\Http\Transformers\ACTIVO_FIJO\ListaUsuarioTransformer;
use App\Services\ACTIVO_FIJO\ListaUsuarioService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class ListaUsuarioController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ListaUsuarioService
     */
    protected $service;

    /**
     * @var ListaUsuarioTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param ListaUsuarioService $service
     * @param ListaUsuarioTransformer $transformer
     */
    public function __construct(Manager $fractal, ListaUsuarioService $service, ListaUsuarioTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function indexOrdenado(Request $request)
    {
        return $this->service->indexOrdenado($request->all());
    }
}

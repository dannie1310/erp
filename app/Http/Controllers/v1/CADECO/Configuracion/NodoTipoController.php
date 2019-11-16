<?php


namespace App\Http\Controllers\v1\CADECO\Configuracion;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Configuracion\NodoTipoTransformer;
use App\Services\CADECO\Configuracion\NodoTipoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class NodoTipoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var NodoTipoService
     */
    protected $service;

    /**
     * @var NodoTipoTransformer
     */
    protected $transformer;

    /**
     * NodoTipoController constructor.
     * @param Manager $fractal
     * @param NodoTipoService $service
     * @param NodoTipoTransformer $transformer
     */
    public function __construct(Manager $fractal, NodoTipoService $service, NodoTipoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
//        $this->middleware('permiso:editar_salida_almacen')->only(['destroy','update']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

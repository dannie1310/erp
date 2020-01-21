<?php


namespace App\Http\Controllers\v1\CADECO\Configuracion;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Configuracion\NodoProyectoTransformer;
use App\Models\CADECO\NodoProyecto;
use App\Services\CADECO\Configuracion\NodoProyectoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class NodoProyectoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var NodoProyectoService
     */
    protected $service;

    /**
     * @var NodoProyectoTransformer
     */
    protected $transformer;

    /**
     * NodoProyectoController constructor.
     * @param Manager $fractal
     * @param NodoProyectoService $service
     * @param NodoProyectoTransformer $transformer
     */
    public function __construct(Manager $fractal, NodoProyectoService $service, NodoProyectoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
//        $this->middleware('permiso:editar_salida_almacen')->only(['destroy','update']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}

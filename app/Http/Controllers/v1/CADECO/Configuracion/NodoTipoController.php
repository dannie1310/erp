<?php


namespace App\Http\Controllers\v1\CADECO\Configuracion;


use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ventas\StoreNodoTipoRequest;
use App\Services\CADECO\Configuracion\NodoTipoService;
use App\Http\Transformers\CADECO\Configuracion\NodoTipoTransformer;

class NodoTipoController extends Controller
{
    use ControllerTrait{
        store as protected traitStore;
    }

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

    public function store(StoreNodoTipoRequest $request)
    {
        // dd($request->all());
        return $this->traitStore($request);
    }
}

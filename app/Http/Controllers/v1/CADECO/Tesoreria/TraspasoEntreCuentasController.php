<?php

namespace App\Http\Controllers\v1\CADECO\Tesoreria;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Tesoreria\TraspasoCuentasTransformer;
use App\Services\CADECO\Tesoreria\TraspasoEntreCuentasService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TraspasoEntreCuentasController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TraspasoEntreCuentasService
     */
    protected $service;

    /**
     * @var TraspasoCuentasTransformer
     */
    protected $transformer;

    /**
     * TraspasoEntreCuentasController constructor.
     * @param Manager $fractal
     * @param TraspasoEntreCuentasService $service
     * @param TraspasoCuentasTransformer $transformer
     */
    public function __construct(Manager $fractal, TraspasoEntreCuentasService $service, TraspasoCuentasTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_traspaso_cuenta')->only(['paginate','show']);
        $this->middleware('permiso:editar_traspaso_cuenta')->only(['update']);
        $this->middleware('permiso:eliminar_traspaso_cuenta')->only(['delete','destroy']);
        $this->middleware('permiso:registrar_traspaso_cuenta')->only(['store','create']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
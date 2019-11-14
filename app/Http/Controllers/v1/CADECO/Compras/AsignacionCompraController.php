<?php


namespace App\Http\Controllers\v1\CADECO\Compras;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Compras\AsignacionCompraTransformer;
use App\Services\CADECO\AsignacionCompraService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;


class AsignacionCompraController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var AsignacionCompraService
     */
    protected $service;

    /**
     * @var AsignacionCompraTransformer
     */
    protected $transformer;

    /**
     * SolicitudCompraController constructor.
     * @param Manager $fractal
     * @param AsignacionCompraService $service
     * @param AsignacionCompraTransformer $transformer
     */
    public function __construct(Manager $fractal, AsignacionCompraService $service, AsignacionCompraTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
//        $this->middleware('permiso:consultar_asignacion_compra')->only('paginate');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

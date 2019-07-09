<?php


namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Compras\SolicitudCompraTransformer;
use App\Services\CADECO\Compras\SolicitudCompraService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SolicitudCompraController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SolicitudCompraService
     */
    protected $service;

    /**
     * @var SolicitudCompraTransformer
     */
    protected $transformer;

    /**
     * SolicitudCompraController constructor.
     * @param Manager $fractal
     * @param SolicitudCompraService $service
     * @param SolicitudCompraTransformer $transformer
     */
    public function __construct(Manager $fractal, SolicitudCompraService $service, SolicitudCompraTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_solicitud_compra')->only('paginate');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
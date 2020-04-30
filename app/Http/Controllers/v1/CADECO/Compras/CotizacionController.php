<?php


namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\CotizacionTransformer;
use App\Services\CADECO\Compras\CotizacionService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CotizacionController extends Controller
{
    use ControllerTrait {
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CotizacionService
     */
    private $service;

    /**
     * @var CotizacionTransformer
     */
    private $transformer;

    /**
     * SalidaAlmacenController constructor.
     * @param Manager $fractal
     * @param CotizacionService $service
     * @param CotizacionTransformer $transformer
     */

    public function __construct(Manager $fractal, CotizacionService $service, CotizacionTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:registrar_cotizacion_compra')->only(['store']);
        $this->middleware('permiso:consultar_cotizacion_compra')->only(['show','paginate','index','find']);
        $this->middleware('permiso:editar_cotizacion_compra')->only(['update']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function descargaLayout($id)
    {
        return $this->service->descargaLayout($id);
    }
}
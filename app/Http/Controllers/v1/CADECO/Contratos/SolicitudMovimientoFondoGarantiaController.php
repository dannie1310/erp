<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 06/02/2019
 * Time: 06:03 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contratos;


use App\Http\Controllers\Controller;
use App\Http\Requests\Subcontratos\AutorizarSolicitudMovimientoFondoGarantiaRequest;
use App\Http\Requests\Subcontratos\CancelarSolicitudMovimientoFondoGarantiaRequest;
use App\Http\Requests\Subcontratos\RechazarSolicitudMovimientoFondoGarantiaRequest;
use App\Http\Requests\Subcontratos\RevertirAutorizacionSolicitudMovimientoFondoGarantiaRequest;
use App\Http\Requests\Subcontratos\ShowSolicitudMovimientoFondoGarantiaRequest;
use App\Http\Requests\Subcontratos\StoreSolicitudMovimientoFondoGarantiaRequest;
use App\Http\Transformers\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantiaTransformer;
use App\Services\CADECO\Contratos\SolicitudMovimientoFondoGarantiaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SolicitudMovimientoFondoGarantiaController extends Controller
{
    use ControllerTrait {
        store as protected traitStore;
        show as protected traitShow;
        paginate as protected traitPaginate;
        index as protected traitIndex;
    }
    /*use ControllerTrait {update as protected traitUpdate; store as protected traitStore; }*/

    /**
     * @var SolicitudMovimientoFondoGarantiaService
     */
    private $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SolicitudMovimientoFondoGarantiaTransformer
     */
    protected $transformer;

    /**
     * SolicitudMovimientoFondoGarantiaController constructor.
     * @param SolicitudMovimientoFondoGarantiaService $service
     * @param Manager $fractal
     * @param SolicitudMovimientoFondoGarantiaTransformer $transformer
     */
    public function __construct(SolicitudMovimientoFondoGarantiaService $service, Manager $fractal, SolicitudMovimientoFondoGarantiaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:autorizar_solicitud_movimiento_fondo_garantia')->only('autorizar');
        $this->middleware('permiso:cancelar_solicitud_movimiento_fondo_garantia')->only('cancelar');
        $this->middleware('permiso:consultar_solicitud_movimiento_fondo_garantia')->only(['show','paginate','index']);
        $this->middleware('permiso:rechazar_solicitud_movimiento_fondo_garantia')->only('rechazar');
        $this->middleware('permiso:registrar_solicitud_movimiento_fondo_garantia')->only('store');
        $this->middleware('permiso:revertir_autorizacion_solicitud_movimiento_fondo_garantia')->only('revertirAutorizacion');
        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function autorizar(AutorizarSolicitudMovimientoFondoGarantiaRequest $request, $id)
    {
        $item = $this->service->autorizar($request->all(), $id);
        return $this->respondWithItem($item);
    }

    public function cancelar(CancelarSolicitudMovimientoFondoGarantiaRequest $request, $id)
    {
        $item = $this->service->cancelar($request->all(), $id);
        return $this->respondWithItem($item);
    }

    public function rechazar(RechazarSolicitudMovimientoFondoGarantiaRequest $request, $id)
    {
        $item = $this->service->rechazar($request->all(), $id);
        return $this->respondWithItem($item);
    }

    public function revertirAutorizacion(RevertirAutorizacionSolicitudMovimientoFondoGarantiaRequest $request, $id)
    {
        $item = $this->service->revertirAutorizacion($request->all(), $id);
        return $this->respondWithItem($item);
    }

    public function store(StoreSolicitudMovimientoFondoGarantiaRequest $request)
    {
        return $this->traitStore($request);
    }

    public function show(ShowSolicitudMovimientoFondoGarantiaRequest $request, $id)
    {
        return $this->traitShow($request, $id);
    }

    public function paginate(ShowSolicitudMovimientoFondoGarantiaRequest $request)
    {
        return $this->traitPaginate($request);
    }

    public function index(ShowSolicitudMovimientoFondoGarantiaRequest $request)
    {
        return $this->traitIndex($request);
    }
}
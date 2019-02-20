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
use App\Http\Requests\Subcontratos\StoreSolicitudMovimientoFondoGarantiaRequest;
use App\Http\Transformers\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantiaTransformer;
use App\Services\CADECO\Contratos\SolicitudMovimientoFondoGarantiaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SolicitudMovimientoFondoGarantiaController extends Controller
{
    use ControllerTrait {update as protected traitUpdate; store as protected traitStore; }

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
     * CuentaAlmacenController constructor.
     * @param SolicitudMovimientoFondoGarantiaService $service
     * @param Manager $fractal
     * @param SolicitudMovimientoFondoGarantiaTransformer $transformer
     */
    public function __construct(SolicitudMovimientoFondoGarantiaService $service, Manager $fractal, SolicitudMovimientoFondoGarantiaTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function store(StoreSolicitudMovimientoFondoGarantiaRequest $request)
    {
        return $this->traitStore($request);
    }

    public function cancelar(CancelarSolicitudMovimientoFondoGarantiaRequest $request, $id)
    {
        $item = $this->service->cancelar($request, $id);
        return $this->respondWithItem($item);
    }

    public function autorizar(AutorizarSolicitudMovimientoFondoGarantiaRequest $request, $id)
    {
        $item = $this->service->autorizar($request, $id);
        return $this->respondWithItem($item);
    }

    public function rechazar(RechazarSolicitudMovimientoFondoGarantiaRequest $request, $id)
    {
        $item = $this->service->rechazar($request, $id);
        return $this->respondWithItem($item);
    }

    public function revertirAutorizacion(RevertirAutorizacionSolicitudMovimientoFondoGarantiaRequest $request, $id)
    {
        $item = $this->service->revertirAutorizacion($request, $id);
        return $this->respondWithItem($item);
    }
}
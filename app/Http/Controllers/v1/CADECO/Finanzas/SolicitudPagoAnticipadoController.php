<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 12:55 PM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSolicitudPagoAnticipadoRequest;
use App\Http\Transformers\CADECO\Finanzas\SolicitudPagoAnticipadoTransformer;
use App\Services\CADECO\Finanzas\SolicitudPagoAnticipadoService;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use App\Traits\ControllerTrait;

class SolicitudPagoAnticipadoController extends Controller
{
    use ControllerTrait {
        store as protected traitStore;
    }

    /**
     * @var SolicitudPagoAnticipadoService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var SolicitudPagoAnticipadoTransformer
     */
    private $transformer;

    /**
     * SolicitudPagoAnticipadoController constructor.
     * @param SolicitudPagoAnticipadoService $service
     * @param Manager $fractal
     * @param SolicitudPagoAnticipadoTransformer $transformer
     */
    public function __construct(SolicitudPagoAnticipadoService $service, Manager $fractal, SolicitudPagoAnticipadoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:registrar_solicitud_pago_anticipado')->only('store');
        $this->middleware('permiso:cancelar_solicitud_pago_anticipado')->only('cancelar');
        $this->middleware('permiso:editar_solicitud_pago_anticipado')->only('update');
        $this->middleware('permiso:consultar_solicitud_pago_anticipado')->only(['pdfPagoAnticipado','show','paginate','index','find']);

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function store(StoreSolicitudPagoAnticipadoRequest $request)
    {
        return $this->traitStore($request);
    }

    public function cancelar(Request $request, $id)
    {
        $item = $this->service->cancelar($request->all(), $id);
        return $this->respondWithItem($item);
    }

    public function pdfPagoAnticipado($id){

        return $this->service->pdfPagoAnticipado($id)->create();
    }

    public function getIndicadorAplicadas()
    {
        return $this->service->getIndicadorAplicadas();
    }

    public function solicitarAutorizacion(Request $request, $id)
    {
        $item = $this->service->solicitarAutorizacion($request->all(), $id);
        return $this->respondWithItem($item);
    }
}

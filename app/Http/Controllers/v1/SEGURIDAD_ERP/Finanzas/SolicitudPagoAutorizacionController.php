<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\SolicitudPagoTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionTransformer;
use App\Services\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionService;
use App\Services\SEGURIDAD_ERP\Finanzas\SolicitudPagoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class SolicitudPagoAutorizacionController extends Controller
{
    use ControllerTrait {
        store as protected traitStore;
    }

    /**
     * @var SolicitudPagoService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var SolicitudPagoTransformer
     */
    private $transformer;

    /**
     * @param SolicitudPagoAutorizacionService $service
     * @param Manager $fractal
     * @param SolicitudPagoAutorizacionTransformer $transformer
     */
    public function __construct(SolicitudPagoAutorizacionService $service, Manager $fractal, SolicitudPagoAutorizacionTransformer $transformer)
    {
        $this->middleware('auth:api');
        //$this->middleware('context');

        /*$this->middleware('permiso:registrar_solicitud_pago_anticipado')->only('store');
        $this->middleware('permiso:cancelar_solicitud_pago_anticipado')->only('cancelar');
        $this->middleware('permiso:editar_solicitud_pago_anticipado')->only('update');
        $this->middleware('permiso:consultar_solicitud_pago_anticipado')->only(['pdfPagoAnticipado','show','paginate','index','find']);
*/
        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function autorizar($id)
    {
        $this->service->show($id)->autorizar();
    }

    public function rechazar(Request $request, $id)
    {
        $this->service->show($id)->rechazar($request->motivo);
    }
}

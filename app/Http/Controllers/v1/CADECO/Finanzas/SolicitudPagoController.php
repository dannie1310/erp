<?php

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSolicitudPagoAnticipadoRequest;
use App\Http\Transformers\CADECO\Finanzas\SolicitudPagoAnticipadoTransformer;
use App\Http\Transformers\CADECO\Finanzas\SolicitudPagoTransformer;
use App\Services\CADECO\Finanzas\SolicitudPagoAnticipadoService;
use App\Services\CADECO\Finanzas\SolicitudPagoService;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use App\Traits\ControllerTrait;

class SolicitudPagoController extends Controller
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
     * @param SolicitudPagoService $service
     * @param Manager $fractal
     * @param SolicitudPagoTransformer $transformer
     */
    public function __construct(SolicitudPagoService $service, Manager $fractal, SolicitudPagoTransformer $transformer)
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
}

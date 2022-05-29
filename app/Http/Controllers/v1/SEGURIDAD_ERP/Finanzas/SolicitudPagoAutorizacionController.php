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
        $this->middleware('permisoGlobal:autorizar_rechazar_solicitud_pago')->only(['autorizar','rechazar']);
        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function autorizar(Request $request,$id)
    {
        return $this->service->show($id)->autorizar();
    }

    public function rechazar(Request $request, $id)
    {
        return $this->service->show($id)->rechazar($request->motivo);
    }

    public function autorizarVista(Request $request,$id)
    {
        $solicitud = $this->service->show($id);
        try{
            $solicitud->autorizar();
        }catch (\Exception $e)
        {
            return view('finanzas.solicitud_pago_anticipado', ['solicitud' => $solicitud
                , "error" => $e->getMessage(),
                "token"=>$request->access_token, "mensaje"=>null
            ]);
        }
        return view('finanzas.solicitud_pago_anticipado', [
            'solicitud' => $solicitud
            , "token"=>$request->access_token
            , "error"=>null, "mensaje"=>"Solicitud Autorizada Correctamente"
        ]);

    }

    public function pideMotivoRechazoVista(Request $request,$id)
    {
        $solicitud = $this->service->show($id);
        return view('finanzas.motivo_rechazo_solicitud_pago_anticipado', [
            'solicitud' => $solicitud,
            "token"=>$request->access_token
        ]);
    }

    public function rechazarVista(Request $request,$id)
    {
        $solicitud = $this->service->show($id);
        try{
            $this->service->show($id)->rechazar($request->motivo);
        }catch (\Exception $e)
        {
            return view('finanzas.solicitud_pago_anticipado', ['solicitud' => $solicitud
                , "error" => $e->getMessage(),"token"=>$request->access_token, "mensaje"=>null
            ]);
        }
        return view('finanzas.solicitud_pago_anticipado', ['solicitud' => $solicitud
            ,"token"=>$request->access_token
            , "error"=>null, "mensaje"=>"Solicitud Rechazada Correctamente"
        ]);

    }

    public function showVista(Request $request,$id)
    {
        $solicitud = $this->service->show($id);
        return view('finanzas.solicitud_pago_anticipado', ['solicitud' => $solicitud
            , "token" => $request->get('access_token')
            , "error" => null, "mensaje"=>null
        ]);
    }

    public function indexVista(Request $request)
    {
        $solicitudes = $this->service->porAutorizar();
        return view('finanzas.solicitudes_pago_anticipado',
            [
                'solicitudes' => $solicitudes
            , "token" => $request->get('access_token')
        ]);
    }
}

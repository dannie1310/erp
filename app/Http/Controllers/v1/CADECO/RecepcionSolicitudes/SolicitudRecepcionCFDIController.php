<?php


namespace App\Http\Controllers\v1\CADECO\RecepcionSolicitudes;


use App\Http\Controllers\Controller;
use App\Traits\ControllerTrait;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDITransformer as Transformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use App\Services\CADECO\RecepcionCFDI\SolicitudRecepcionCFDIService as Service;

class SolicitudRecepcionCFDIController extends Controller
{
    use ControllerTrait;

    public function __construct(Service $service, Manager $fractal, Transformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware("context");
        $this->middleware('permiso:aprobar_solicitud_recepcion_cfdi')->only('aprobar');
        $this->middleware('permiso:rechazar_solicitud_recepcion_cfdi')->only('rechazar');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function aprobar(Request $request , $id)
    {
        $this->service->aprobar($request->all(),$id);
    }

    public function rechazar(Request $request , $id){
        $item = $this->service->rechazar($request->all(),$id);
        return $this->respondWithItem($item);
    }

}

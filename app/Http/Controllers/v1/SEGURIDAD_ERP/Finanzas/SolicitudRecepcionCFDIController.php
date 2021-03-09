<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas;


use App\Http\Controllers\Controller;
use App\Traits\ControllerTrait;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDITransformer as Transformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use App\Services\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDIService as Service;

class SolicitudRecepcionCFDIController extends Controller
{
    use ControllerTrait;

    public function __construct(Service $service, Manager $fractal, Transformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('permisoGlobal:cancelar_solicitud_recepcion_cfdi')->only('cancelar');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function cancelar(Request $request , $id){
        $item = $this->service->cancelar($request->all(),$id);
        return $this->respondWithItem($item);
    }

    public function pdfSolicitudRecepcion($id)
    {
        $this->service->solicitudRecepcionPDF($id)->create();
    }

}

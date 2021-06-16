<?php


namespace App\Http\Controllers\v1\CADECO\Contratos;

use App\Http\Controllers\Controller;
use App\Traits\ControllerTrait;
use App\Services\CADECO\Contratos\SolicitudCambioSubcontratoService as Service;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use App\Http\Transformers\CADECO\Contrato\SolicitudCambioSubcontratoTransformer as Transformer;

class SolicitudCambioController extends Controller
{
    use ControllerTrait;
    public function __construct(Service $service, Manager $fractal, Transformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_solicitud_cambio_subcontrato')->only(['index','paginate','find','show', 'pdf']);
        $this->middleware('permiso:registrar_solicitud_cambio_subcontrato')->only(['registrar']);
        $this->middleware('permiso:cancelar_solicitud_cambio_subcontrato')->only('cancelar');
        $this->middleware('permiso:aplicar_solicitud_cambio_subcontrato')->only(['aplicar','rechazar']);

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function registrar(Request $request)
    {
        $respuesta = $this->service->registrar($request);
        return $this->respondWithItem($respuesta);
    }

    public function pdf($id)
    {
        return $this->service->pdf($id)->create();
    }

    public function aplicar($id)
    {
        $respuesta = $this->service->show($id)->aplicar();
        return $this->respondWithItem($respuesta);
    }

    public function cancelar($id,Request $request)
    {
        $respuesta = $this->service->show($id)->cancelar($request->params["motivo"]);
        return $this->respondWithItem($respuesta);
    }

    public function rechazar($id,Request $request)
    {
        $respuesta = $this->service->show($id)->rechazar($request->motivo);
        return $this->respondWithItem($respuesta);
    }

    public function procesarLayoutExtraordinarios(Request $request){
        $respuesta = $this->service->procesarLayoutExtraordinarios($request);
        return response()->json($respuesta, 200);
    }

    public function procesarLayoutCambioPrecioVolumen(Request $request){
        $respuesta = $this->service->procesarLayoutCambioPrecioVolumen($request);
        return response()->json($respuesta, 200);
    }
}

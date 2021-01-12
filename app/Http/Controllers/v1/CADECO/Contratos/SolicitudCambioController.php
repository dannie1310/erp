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

        /*$this->middleware('permiso:consultar_solicitud_cambio')->only(['index','paginate','find','show', 'pdf']);
        $this->middleware('permiso:registrar_solicitud_cambio')->only(['store']);
        $this->middleware('permiso:eliminar_solicitud_cambio')->only('destroy');*/

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function registrar(Request $request)
    {
        $respuesta = $this->service->registrar($request);
        return $this->respondWithItem($respuesta);
    }
}

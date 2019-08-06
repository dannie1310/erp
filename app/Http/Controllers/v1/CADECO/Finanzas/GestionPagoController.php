<?php


namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Requests\Finanzas\StoreDistribucionRecursoRemesaRequest;
use App\Http\Transformers\CADECO\Finanzas\DistribucionRecursoRemesaTransformer;
use App\Services\CADECO\Finanzas\DistribucionRecursoRemesaService;
use App\Services\CADECO\Finanzas\GestionPagoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class GestionPagoController extends Controller
{
    use ControllerTrait{
        store as protected traitStore;
    }

    /**
     * @var DistribucionRecursoRemesaService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var DistribucionRecursoRemesaTransformer
     */
    private $transformer;

    /**
     * GestionPagoController constructor.
     * @param GestionPagoService $service
     * @param Manager $fractal
     */
    public function __construct(GestionPagoService $service, Manager $fractal, DistribucionRecursoRemesaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_pagos')->only(['paginate','show']);
        $this->middleware('permiso:cargar_bitacora')->only(['presentaBitacora']);
//        $this->middleware('permiso:registrar_pagos_bitacora')->only(['registrarPagos']);

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function presentaBitacora(Request $request){
        $respuesta = $this->service->validarBitacora($request->bitacora);
//        $respuesta = $this->service->validarBitacora($request->file('file'));
        return response()->json($respuesta, 200);
    }

    public function registrarPagos(Request $request)
    {
        $respuesta = $this->service->registrarPagos($request->toArray());
        dd('koala', $respuesta);
        return response()->json($respuesta, 200);
    }
}

<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\FacturaTransformer;
use App\Services\CONTROLRECURSOS\FacturaService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class FacturaController extends Controller
{
    use ControllerTrait;

    /**
     * @var FacturaService
     */
    protected $service;

    /**
     * @var FacturaTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param FacturaService $service
     * @param FacturaTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(FacturaService $service, FacturaTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');
        $this->middleware('permisoGlobal:consultar_factura_recursos')->only(['show','paginate','index']);
        $this->middleware('permisoGlobal:registrar_factura_recursos')->only(['store']);

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }

    public function cargaXML(Request $request)
    {
        $respuesta = $this->service->cargaXML($request->all());
        return response()->json($respuesta, 200);
    }
}

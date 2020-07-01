<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 12/05/2020
 * Time: 11:33 PM
 */

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\PolizasCtpqIncidentes;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaTransformer as Transformer;
use App\Services\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaService as Service;
use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;

class DiferenciaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var Service
     */
    protected $service;

    /**
     * @var Transformer
     */
    protected $transformer;

    /**
     * IncidenteController constructor.
     * @param Manager $fractal
     * @param Service $service
     * @param Transformer $transformer
     */
    public function __construct(Manager $fractal, Service $service, Transformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function show(Request $request, $id)
    {
        $item = $this->service->show($request->all(), $id);
        return $this->respondWithItem($item);
    }

    public function buscarDiferencias(Request $request)
    {
        $respuesta =$this->service->buscarDiferencias($request);
        return response()->json($respuesta, 200);
    }

    public function obtenerInforme(Request $request)
    {
        $respuesta =$this->service->obtenerInforme($request);
        return response()->json($respuesta, 200);
    }

    public function impresionPolizas($id)
    {
        return $this->service->impresionPolizas($id)->create();
    }

}
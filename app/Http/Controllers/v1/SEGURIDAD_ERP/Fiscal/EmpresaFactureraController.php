<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 24/06/2020
 * Time: 02:12 PM
 */

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal;


use App\Http\Controllers\Controller;
use App\Services\SEGURIDAD_ERP\Fiscal\EmpresaFactureraService as Service;
use App\Http\Transformers\SEGURIDAD_ERP\Fiscal\EmpresaFactureraTransformer as Transformer;
use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;


class EmpresaFactureraController extends Controller
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

    public function buscarCoincidencias(Request $request)
    {
        $respuesta =$this->service->buscarCoincidencias($request);
        return response()->json($respuesta, 200);
    }

}
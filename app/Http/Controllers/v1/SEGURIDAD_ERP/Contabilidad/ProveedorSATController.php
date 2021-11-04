<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\SEGURIDAD_ERP\Contabilidad\ProveedorSATService;
use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\ProveedorSATTransformer;

class ProveedorSATController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ProveedorSATService
     */
    protected $service;

    /**
     * @var ProveedorSATTransformer
     */
    protected $transformer;

    /**
     * IncidenciaController constructor.
     * @param Manager $fractal
     * @param ProveedorSATService $service
     * @param ProveedorSATTransformer $transformer
     */
    public function __construct(Manager $fractal, ProveedorSATService $service, ProveedorSATTransformer $transformer)
    {
        $this->middleware( 'auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function buscarProveedorAsociar(Request $request)
    {
        $resp = $this->service->buscarProveedorAsociar($request->all());
        return $this->respondWithCollection($resp);
    }
}

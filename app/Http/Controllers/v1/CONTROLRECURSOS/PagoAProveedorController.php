<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\PagoAProveedorTransformer;
use App\Services\CONTROLRECURSOS\PagoAProveedorService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class PagoAProveedorController extends Controller
{
    use ControllerTrait;

    /**
     * @var PagoAProveedorService
     */
    protected $service;

    /**
     * @var PagoAProveedorTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    public function __construct(PagoAProveedorService $service, Manager $fractal, PagoAProveedorTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function editar(Request $request)
    {dd($request->all());
        $item = $this->service->editar($request->all());
        return $this->respondWithItem($item);
    }
}

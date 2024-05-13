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

        $this->middleware('permisoGlobal:consultar_solicitud_pago_reembolso')->only(['show','paginate','index']);
        $this->middleware('permisoGlobal:registrar_solicitud_pago_reembolso')->only(['store']);
        $this->middleware('permisoGlobal:editar_solicitud_pago_reembolso')->only(['update']);
        $this->middleware('permisoGlobal:eliminar_solicitud_pago_reembolso')->only(['destroy']);

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}

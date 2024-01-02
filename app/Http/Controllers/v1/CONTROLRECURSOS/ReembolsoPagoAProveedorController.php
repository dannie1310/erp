<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\ReembolsoPagoAProveedorTransformer;
use App\Services\CONTROLRECURSOS\ReembolsoPagoAProveedorService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ReembolsoPagoAProveedorController extends Controller
{
    use ControllerTrait;

    /**
     * @var ReembolsoPagoAProveedorService
     */
    protected $service;

    /**
     * @var ReembolsoPagoAProveedorTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param ReembolsoPagoAProveedorService $service
     * @param ReembolsoPagoAProveedorTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(ReembolsoPagoAProveedorService $service, ReembolsoPagoAProveedorTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');


        $this->middleware('permisoGlobal:consultar_reembolso_pago_a_proveedor')->only(['show','paginate','index']);
        $this->middleware('permisoGlobal:registrar_reembolso_pago_a_proveedor')->only(['store']);
        $this->middleware('permisoGlobal:editar_reembolso_pago_a_proveedor')->only(['update']);
        $this->middleware('permisoGlobal:eliminar_reembolso_pago_a_proveedor')->only(['destroy']);

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

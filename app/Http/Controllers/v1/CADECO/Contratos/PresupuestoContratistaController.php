<?php


namespace App\Http\Controllers\v1\CADECO\Contratos;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contrato\PresupuestoContratistaTransformer;
use App\Services\CADECO\Contratos\PresupuestoContratistaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class PresupuestoContratistaController extends Controller
{
    use ControllerTrait {
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var PresupuestoContratistaService
     */
    private $service;

    /**
     * @var PresupuestoContratistaTransformer
     */
    private $transformer;

    /**
     * PresupuestoContratistaController constructor
     * @param Manager $fractal
     * @param PresupuestoContratistaService $service
     * @param PresupuestoContratistaTransformer $transformer
     */

     public function __construct(Manager $fractal, PresupuestoContratistaService $service, PresupuestoContratistaTransformer $transformer)
     {
         $this->middleware('auth:api');         
         $this->middleware('context');
         $this->middleware('permiso:consultar_presupuesto_contratista')->only(['show','paginate','index','find']);
         $this->middleware('permiso:editar_presupuesto_contratista')->only('update');

         $this->fractal = $fractal;
         $this->service = $service;
         $this->transformer = $transformer;
     }
}
<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas;

use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\TransaccionesEfosTransformer;
use App\Services\SEGURIDAD_ERP\Finanzas\TransaccionesEfosService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TransaccionesEfosController extends Controller
{
    use ControllerTrait;

    /**
     * @var TransaccionesEfosService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var TransaccionesEfosTransformer
     */
    private $transformer;

    /**
     * TransaccionesEfosController constructor
     * @param TransaccionesEfosService $service
     * @param Manager $fractal
     * @param TransaccionesEfosTransformer $transformer
     */

     public function __construct(TransaccionesEfosService $service, Manager $fractal, TransaccionesEfosTransformer $transformer)
     {
         $this->middleware('auth:api');
         $this->middleware('context');
         $this->middleware('permiso:consultar_transacciones_efos')->only(['paginate']);


        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
         
     }
}
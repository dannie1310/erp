<?php


namespace App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\SubcontratosEstimaciones\PenalizacionLiberacionTransformer;
use App\Services\CADECO\SubcontratosEstimaciones\PenalizacionLiberacionService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class PenalizacionLiberacionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var PenalizacionLiberacionService
     */
    protected $service;

    /**
     * @var PenalizacionLiberacionTransformer
     */
    protected $transformer;

    /**
     * PenalizacionLiberacionController
     * 
     * @param Manager $fractal
     * @param PenalizacionLiberacion $service
     * @param PenalizacionLiberacionTransformer $transformer
     */

     public function __construct(Manager $fractal, PenalizacionLiberacionService $service, PenalizacionLiberacionTransformer $transformer)
     {
         $this->middleware('auth:api');         
         $this->middleware('context');
         $this->middleware('permiso:registrar_liberacion_penalizacion_estimacion_subcontrato')->only(['store']);
         $this->middleware('permiso:eliminar_liberacion_penalizacion_estimacion_subcontrato')->only(['destroy']);

         $this->fractal = $fractal;
         $this->service = $service;         
         $this->transformer = $transformer;
     }
}
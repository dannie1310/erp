<?php


namespace App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\SubcontratosEstimaciones\PenalizacionTransformer;
use App\Services\CADECO\SubcontratosEstimaciones\PenalizacionService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class PenalizacionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var PenalizacionService
     */
    protected $service;

    /**
     * @PenalizacionTransformer
     */
    protected $transformer;

    /**
     * PenalizacionController constructor
     * 
     * @param Manager $fractal
     * @param PenalizacionService $service
     * @param PenalizacionTransformer $transformer
     */
     
     public function __construct(Manager $fractal, PenalizacionService $service, PenalizacionTransformer $transformer)
     {
        //  dd('Penalizacion controller');
         $this->middleware('auth:api');
         $this->middleware('context');

         $this->fractal = $fractal;
         $this->service = $service;
         $this->transformer = $transformer;         
     }

}
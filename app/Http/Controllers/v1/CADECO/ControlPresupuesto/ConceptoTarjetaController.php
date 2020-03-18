<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 13/03/2020
 * Time: 02:58 PM
 */

namespace App\Http\Controllers\v1\CADECO\ControlPresupuesto;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\CADECO\ControlPresupuesto\ConceptoTarjetaService;
use App\Http\Transformers\CADECO\ControlPresupuesto\ConceptoTarjetaTransformer;

class ConceptoTarjetaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ConceptoTarjetaService
     */
    protected $service;


    /**
     * @var ConceptoTarjetaTransformer
     */
    protected $transformer;


    /**
     * TipoOrdenController constructor
     *
     * @param Manager $fractal
     * @param ConceptoTarjetaService $service
     * @param ConceptoTarjetaTransformer $transformer
     */

    public function __construct(Manager $fractal, ConceptoTarjetaService $service, ConceptoTarjetaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        // $this->middleware('permiso:consultar_pagos')->only(['paginate']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function conceptosTarjeta($id){
        $lista = $this->service->list($id);
        return $this->respondWithCollection($lista);
    }

}

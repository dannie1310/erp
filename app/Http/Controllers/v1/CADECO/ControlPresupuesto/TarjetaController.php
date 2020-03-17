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
use App\Services\CADECO\ControlPresupuesto\TarjetaService;
use App\Http\Transformers\CADECO\ControlPresupuesto\TarjetaTransformer;

class TarjetaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TarjetaService
     */
    protected $service;


    /**
     * @var TarjetaTransformer
     */
    protected $transformer;


    /**
     * TipoOrdenController constructor
     *
     * @param Manager $fractal
     * @param TarjetaService $service
     * @param TarjetaTransformer $transformer
     */

    public function __construct(Manager $fractal, TarjetaService $service, TarjetaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        // $this->middleware('permiso:consultar_pagos')->only(['paginate']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}

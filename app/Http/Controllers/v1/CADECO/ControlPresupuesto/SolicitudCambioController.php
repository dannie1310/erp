<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 0:58 PM
 */

namespace App\Http\Controllers\v1\CADECO\ControlPresupuesto;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\CADECO\ControlPresupuesto\SolicitudCambioService;
use App\Http\Transformers\CADECO\ControlPresupuesto\SolicitudCambioTransformer;

class SolicitudCambioController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SolicitudCambioService
     */
    protected $service;


    /**
     * @var SolicitudCambioTransformer
     */
    protected $transformer;


    /**
     * SolicitudCambioController constructor
     *
     * @param Manager $fractal
     * @param SolicitudCambioService $service
     * @param SolicitudCambioTransformer $transformer
     */

    public function __construct(Manager $fractal, SolicitudCambioService $service, SolicitudCambioTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        // $this->middleware('permiso:consultar_pagos')->only(['paginate']);
        // $this->middleware('permiso:consultar_pagos')->only(['store']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}

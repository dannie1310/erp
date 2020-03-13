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
use App\Services\CADECO\ControlPresupuesto\TipoOrdenService;
use App\Http\Transformers\CADECO\ControlPresupuesto\TipoOrdenTransformer;

class TipoOrdenController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TipoOrdenService
     */
    protected $service;


    /**
     * @var TipoOrdenTransformer
     */
    protected $transformer;


    /**
     * TipoOrdenController constructor
     *
     * @param Manager $fractal
     * @param TipoOrdenService $service
     * @param TipoOrdenTransformer $transformer
     */

    public function __construct(Manager $fractal, TipoOrdenService $service, TipoOrdenTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        // $this->middleware('permiso:consultar_pagos')->only(['paginate']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 15/08/19
 * Time: 12:38 PM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\PagoTransformer;
use App\Services\CADECO\Finanzas\PagoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class PagoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var PagoService
     */
    protected $service;


    /**
     * @var PagoTransformer
     */
    protected $transformer;


    /**
     * PagoController constructor
     *
     * @param Manager $fractal
     * @param PagoService $service
     * @param PagoTransformer $transformer
     */

    public function __construct(Manager $fractal, PagoService $service, PagoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_pagos')->only(['paginate', 'show']);
        $this->middleware('permiso:eliminar_pagos')->only(['destroy']);
        $this->middleware('permiso:registrar_pago')->only(['documentosParaPagar', 'documentoParaPagar','store']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function documentosParaPagar()
    {
        return $this->service->documentosParaPagar();
    }

    public function documentoParaPagar($id)
    {
        return $this->service->documentoParaPagar($id);
    }
}

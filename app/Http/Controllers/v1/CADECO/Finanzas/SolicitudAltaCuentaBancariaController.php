<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 07/08/2019
 * Time: 05:09 PM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\SolicitudAltaCuentaBancariaTransformer;
use App\Services\CADECO\Finanzas\SolicitudAltaCuentaBancariaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SolicitudAltaCuentaBancariaController extends Controller
{
    use ControllerTrait;

    /**
     * @var SolicitudAltaCuentaBancariaService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var SolicitudAltaCuentaBancariaTransformer
     */
    private $transformer;

    /**
     * SolicitudAltaCuentaBancariaController constructor.
     * @param SolicitudAltaCuentaBancariaService $service
     * @param Manager $fractal
     * @param SolicitudAltaCuentaBancariaTransformer $transformer
     */
    public function __construct(SolicitudAltaCuentaBancariaService $service, Manager $fractal, SolicitudAltaCuentaBancariaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
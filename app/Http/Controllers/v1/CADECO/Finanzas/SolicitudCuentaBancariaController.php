<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 07/08/2019
 * Time: 05:09 PM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\SolicitudCuentaBancariaTransformer;
use App\Services\CADECO\Finanzas\SolicitudCuentaBancariaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SolicitudCuentaBancariaController extends Controller
{
    use ControllerTrait;

    /**
     * @var SolicitudCuentaBancariaService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var SolicitudCuentaBancariaTransformer
     */
    private $transformer;

    /**
     * SolicitudCuentaBancariaController constructor.
     * @param SolicitudCuentaBancariaService $service
     * @param Manager $fractal
     * @param SolicitudCuentaBancariaTransformer $transformer
     */
    public function __construct(SolicitudCuentaBancariaService $service, Manager $fractal, SolicitudCuentaBancariaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
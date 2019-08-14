<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/08/2019
 * Time: 10:38 AM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\SolicitudCambioCuentaBancariaTransformer;
use App\Services\CADECO\Finanzas\SolicitudCambioCuentaBancariaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SolicitudCambioCuentaBancariaController extends Controller
{
    use ControllerTrait;

    /**
     * @var SolicitudCambioCuentaBancariaService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var SolicitudCambioCuentaBancariaTransformer
     */
    private $transformer;

    /**
     * SolicitudCambioCuentaBancariaController constructor.
     * @param SolicitudCambioCuentaBancariaService $service
     * @param Manager $fractal
     * @param SolicitudCambioCuentaBancariaTransformer $transformer
     */
    public function __construct(SolicitudCambioCuentaBancariaService $service, Manager $fractal, SolicitudCambioCuentaBancariaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_solicitud_cambio_cuenta_bancaria_empresa')->only(['show','paginate','index','find','pdf']);
        $this->middleware('permiso:solicitar_cambio_cuenta_bancaria_empresa')->only('store');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/08/2019
 * Time: 10:38 AM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\SolicitudBajaCuentaBancariaTransformer;
use App\Services\CADECO\Finanzas\SolicitudBajaCuentaBancariaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SolicitudBajaCuentaBancariaController extends Controller
{
    use ControllerTrait;

    /**
     * @var SolicitudBajaCuentaBancariaService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var SolicitudBajaCuentaBancariaTransformer
     */
    private $transformer;

    /**
     * SolicitudCambioCuentaBancariaController constructor.
     * @param SolicitudBajaCuentaBancariaService $service
     * @param Manager $fractal
     * @param SolicitudBajaCuentaBancariaTransformer $transformer
     */
    public function __construct(SolicitudBajaCuentaBancariaService $service, Manager $fractal, SolicitudBajaCuentaBancariaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_solicitud_baja_cuenta_bancaria_empresa')->only(['show','paginate','index','find','pdf']);
        $this->middleware('permiso:solicitar_baja_cuenta_bancaria_empresa')->only('store');
        $this->middleware('permiso:autorizar_solicitud_baja_cuenta_bancaria_empresa')->only('autorizar');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function pdf($id)
    {
        return $this->service->pdf($id);
    }

    public function autorizar($id)
    {
        return $this->respondWithItem($this->service->autorizar($id));
    }
}
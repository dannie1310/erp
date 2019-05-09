<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 12:55 PM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\SolicitudPagoAnticipadoTransformer;
use App\Services\CADECO\Finanzas\SolicitudPagoAnticipadoService;
use League\Fractal\Manager;

class SolicitudPagoAnticipadoController extends Controller
{
    /**
     * @var SolicitudPagoAnticipadoService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var SolicitudPagoAnticipadoTransformer
     */
    private $transformer;

    /**
     * SolicitudPagoAnticipadoController constructor.
     * @param SolicitudPagoAnticipadoService $service
     * @param Manager $fractal
     * @param SolicitudPagoAnticipadoTransformer $transformer
     */
    public function __construct(SolicitudPagoAnticipadoService $service, Manager $fractal, SolicitudPagoAnticipadoTransformer $transformer)
    {
        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
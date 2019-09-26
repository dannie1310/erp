<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 03:58 PM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\CargaLayoutPagoTransformer;
use App\Services\CADECO\Finanzas\CargaLayoutPagoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CargaLayoutPagoController extends Controller
{
    use ControllerTrait;

    /**
     * @var CargaLayoutPagoService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var CargaLayoutPagoTransformer
     */
    protected $transformer;

    /**
     * CargaLayoutPagoController constructor.
     * @param CargaLayoutPagoService $service
     * @param Manager $fractal
     * @param CargaLayoutPagoTransformer $transformer
     */
    public function __construct(CargaLayoutPagoService $service, Manager $fractal, CargaLayoutPagoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
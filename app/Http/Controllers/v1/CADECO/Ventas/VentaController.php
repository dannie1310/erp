<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/12/2019
 * Time: 08:13 PM
 */

namespace App\Http\Controllers\v1\CADECO\Ventas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Ventas\VentaTransformer;
use App\Services\CADECO\Ventas\VentaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class VentaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var VentaService
     */
    private $service;

    /**
     * @var VentaTransformer
     */
    private $transformer;

    /**
     * VentaController constructor.
     * @param Manager $fractal
     * @param VentaService $service
     * @param VentaTransformer $transformer
     */
    public function __construct(Manager $fractal, VentaService $service, VentaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }


}
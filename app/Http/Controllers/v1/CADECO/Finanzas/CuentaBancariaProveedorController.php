<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 30/05/2019
 * Time: 05:28 PM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\CuentaBancariaProveedorTransformer;
use App\Services\CADECO\Finanzas\CuentaBancariaProveedorService;
use League\Fractal\Manager;

class CuentaBancariaProveedorController extends Controller
{
    /**
     * @var CuentaBancariaProveedorService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CuentaBancariaProveedorTransformer
     */
    private $transformer;

    /**
     * CuentaBancariaProveedorController constructor.
     * @param CuentaBancariaProveedorService $service
     * @param Manager $fractal
     * @param CuentaBancariaProveedorTransformer $transformer
     */
    public function __construct(CuentaBancariaProveedorService $service, Manager $fractal, CuentaBancariaProveedorTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
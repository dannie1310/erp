<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 30/05/2019
 * Time: 05:28 PM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;



use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\CuentaBancariaEmpresaTransformer;
use App\Services\CADECO\Finanzas\CuentaBancariaEmpresaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CuentaBancariaEmpresaController extends Controller
{
    use ControllerTrait;
    /**
     * @var CuentaBancariaEmpresaService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CuentaBancariaEmpresaTransformer
     */
    private $transformer;

    /**
     * CuentaBancariaEmpresaController constructor.
     * @param CuentaBancariaEmpresaService $service
     * @param Manager $fractal
     * @param CuentaBancariaEmpresaTransformer $transformer
     */
    public function __construct(CuentaBancariaEmpresaService $service, Manager $fractal, CuentaBancariaEmpresaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
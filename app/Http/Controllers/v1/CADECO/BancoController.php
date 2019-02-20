<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/02/2019
 * Time: 03:08 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\BancoTransformer;
use App\Http\Transformers\CADECO\CuentaBancariaTransformer;
use App\Services\CADECO\BancoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class BancoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var BancoService
     */
    protected $service;

    /**
     * @var CuentaBancariaTransformer
     */
    protected $transformer;

    /**
     * BancoController constructor.
     * @param Manager $fractal
     * @param BancoService $service
     * @param BancoTransformer $transformer
     */
    public function __construct(Manager $fractal, BancoService $service, CuentaBancariaTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');


        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
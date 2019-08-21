<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/08/2019
 * Time: 04:12 PM
 */

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\CtgPlazaTransformer;
use App\Services\SEGURIDAD_ERP\Finanzas\CtgPlazaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CtgPlazaController extends Controller
{
    use ControllerTrait;

    /**
     * @var CtgPlazaService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CtgPlazaTransformer
     */
    private $transformer;

    /**
     * CtgPlazaController constructor.
     * @param CtgPlazaService $service
     * @param Manager $fractal
     * @param CtgPlazaTransformer $transformer
     */
    public function __construct(CtgPlazaService $service, Manager $fractal, CtgPlazaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }


}
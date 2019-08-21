<?php

/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 06/08/2019
 * Time: 12:06 PM
 */

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\CtgBancoTransformer;
use App\Services\SEGURIDAD_ERP\Finanzas\CtgBancoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CtgBancoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var CtgBancoService
     */
    protected $service;

    /**
     * @var CtgBancoTransformer
     */
    protected $transformer;

    /**
     * CtgBancoController constructor
     * @param Manager $fractal
     * @param CtgBancoService $service
     * @param CtgBancoTransformer $transformer
     */

    public function  __construct(Manager $fractal, CtgBancoService $service, CtgBancoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}

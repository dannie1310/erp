<?php

/**
 * Created by PhpStorm.
 * User: Hermes
 * Date: 26/02/2019
 * Time: 05:53 PM
 */
namespace App\Http\Controllers\v1\SEGURIDAD_ERP;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\SistemaTransformer;
use App\Model\SEGURIDAD_ERP\Sistema;
use App\Services\SEGURIDAD_ERP\SistemaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SistemaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SistemaService
     */
    protected $service;

    /**
     * @var SistemaTransformer
     */
    protected $transformer;

    /**
     * SistemaController constructor.
     *
     * @param Manager $fractal
     * @param SistemaService $service
     * @param SistemaTransformer $transformer
     */
    public function __construct(Manager $fractal, SistemaService $service, SistemaTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
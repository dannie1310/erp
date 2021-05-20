<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\ObraTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\SistemaTransformer;
use App\Services\SEGURIDAD_ERP\ObraService;
use App\Services\SEGURIDAD_ERP\SistemaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;
use Illuminate\Http\Request;


class ProyectoController extends Controller
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
    public function __construct(Manager $fractal, ObraService $service, ObraTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}

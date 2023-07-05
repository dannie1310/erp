<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad;


use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\LayoutPasivoCargaTransformer;
use App\Services\SEGURIDAD_ERP\Contabilidad\LayoutPasivoCargaService;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;

class LayoutPasivoCargaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var LayoutPasivoCargaService
     */
    protected $service;

    /**
     * @var LayoutPasivoCargaTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param LayoutPasivoCargaService $service
     * @param LayoutPasivoCargaTransformer $transformer
     */
    public function __construct(Manager $fractal, LayoutPasivoCargaService $service, LayoutPasivoCargaTransformer $transformer)
    {
        $this->middleware( 'auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

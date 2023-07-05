<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad;


use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\LayoutPasivoPartidaTransformer;
use App\Services\SEGURIDAD_ERP\Contabilidad\LayoutPasivoPartidaService;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;

class LayoutPasivoPartidaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var LayoutPasivoPartidaService
     */
    protected $service;

    /**
     * @var LayoutPasivoPartidaTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param LayoutPasivoPartidaService $service
     * @param LayoutPasivoPartidaTransformer $transformer
     */
    public function __construct(Manager $fractal, LayoutPasivoPartidaService $service, LayoutPasivoPartidaTransformer $transformer)
    {
        $this->middleware( 'auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

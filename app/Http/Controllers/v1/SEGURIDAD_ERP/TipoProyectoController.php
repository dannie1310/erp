<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP;

use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\TipoProyectoTransformer;
use App\Services\SEGURIDAD_ERP\TipoProyectoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TipoProyectoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TipoProyectoService
     */
    protected $service;

    /**
     * @var TipoProyectoTransformer
     */
    protected $transformer;

    /**
     * TipoProyectoController constructor.
     * @param Manager $fractal
     * @param TipoProyectoService $service
     * @param TipoProyectoTransformer $transformer
     */
    public function __construct(Manager $fractal, TipoProyectoService $service, TipoProyectoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context')->except('index');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

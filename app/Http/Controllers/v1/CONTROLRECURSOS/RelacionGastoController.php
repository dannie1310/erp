<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\RelacionGastoTransformer;
use App\Services\CONTROLRECURSOS\RelacionGastoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class RelacionGastoController extends Controller
{
    use ControllerTrait;

    /**
     * @var RelacionGastoTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var RelacionGastoService
     */
    protected $service;

    /**
     * @param RelacionGastoTransformer $transformer
     * @param Manager $fractal
     * @param RelacionGastoService $service
     */
    public function __construct(RelacionGastoTransformer $transformer, Manager $fractal, RelacionGastoService $service)
    {
        $this->middleware('auth:api');
        //$this->middleware('permisoGlobal:consultar_factura_recursos')->only(['show','paginate','index']);

        $this->transformer = $transformer;
        $this->fractal = $fractal;
        $this->service = $service;
    }
}

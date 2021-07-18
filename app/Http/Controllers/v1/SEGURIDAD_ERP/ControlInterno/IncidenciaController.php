<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\ControlInterno;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\ControlInterno\IncidenciaTransformer;
use App\Services\SEGURIDAD_ERP\ControlInterno\IncidenciaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class IncidenciaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var IncidenciaService
     */
    protected $service;

    /**
     * @var IncidenciaTransformer
     */
    protected $transformer;

    /**
     * IncidenciaController constructor.
     * @param Manager $fractal
     * @param IncidenciaService $service
     * @param IncidenciaTransformer $transformer
     */
    public function __construct(Manager $fractal, IncidenciaService $service, IncidenciaTransformer $transformer)
    {
        $this->middleware( 'auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

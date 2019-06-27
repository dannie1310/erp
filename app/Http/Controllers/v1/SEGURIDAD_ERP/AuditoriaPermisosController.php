<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\ProyectoTransformer;
use App\Services\SEGURIDAD_ERP\AuditoriaPermisosService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class AuditoriaPermisosController extends Controller
{
    use ControllerTrait {
        store as traitStore;
        destroy as traitDestroy;
    }
    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var AuditoriaPermisosService
     */
    protected $service;

    /**
     * @var ProyectoTransformer
     */
    protected $transformer;

    public function __construct(Manager $fractal, AuditoriaPermisosService $service, ProyectoTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;

    }

}
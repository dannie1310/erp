<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores;


use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\SEGURIDAD_ERP\PadronProveedores\CtgSeccionService;
use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\CtgSeccionTransformer;

class CtgSeccionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SeccionService
     */
    protected $service;

    /**
     * @var SeccionTransformer
     */
    protected $transformer;

    /**
     * EmpresaController constructor.
     * @param Manager $fractal
     * @param SeccionService $service
     * @param SeccionTransformer $transformer
     */
    public function __construct(Manager $fractal, CtgSeccionService $service, CtgSeccionTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}

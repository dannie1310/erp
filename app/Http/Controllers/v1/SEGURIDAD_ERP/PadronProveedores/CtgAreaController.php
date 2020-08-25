<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores;


use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\SEGURIDAD_ERP\PadronProveedores\CtgAreaService;
use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\CtgAreaTransformer;

class CtgAreaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var AreaService
     */
    protected $service;

    /**
     * @var AreaTransformer
     */
    protected $transformer;

    /**
     * EmpresaController constructor.
     * @param Manager $fractal
     * @param SeccionService $service
     * @param SeccionTransformer $transformer
     */
    public function __construct(Manager $fractal, CtgAreaService $service, CtgAreaTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}

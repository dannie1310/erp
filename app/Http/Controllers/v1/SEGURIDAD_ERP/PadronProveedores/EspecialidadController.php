<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\EspecialidadTransformer as Transformer;
use App\Services\SEGURIDAD_ERP\PadronProveedores\EspecialidadService as Service;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class EspecialidadController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var EmpresaService
     */
    protected $service;

    /**
     * @var EmpresaTransformer
     */
    protected $transformer;

    /**
     * GiroController constructor.
     * @param Manager $fractal
     * @param Service $service
     * @param Transformer $transformer
     */
    public function __construct(Manager $fractal, Service $service, Transformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

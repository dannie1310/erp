<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\EmpresaTransformer;
use App\Services\SEGURIDAD_ERP\PadronProveedores\EmpresaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class EmpresaController extends Controller
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
     * EmpresaController constructor.
     * @param Manager $fractal
     * @param EmpresaService $service
     * @param EmpresaTransformer $transformer
     */
    public function __construct(Manager $fractal, EmpresaService $service, EmpresaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('permisoGlobal:iniciar_expediente_proveedor')->only('store');
       // $this->middleware('permisoGlobal:editar_expediente_proveedor')->only('update');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function getDoctosGenerales($id){
        return $this->service->getDoctosGenerales($id);
    }
}

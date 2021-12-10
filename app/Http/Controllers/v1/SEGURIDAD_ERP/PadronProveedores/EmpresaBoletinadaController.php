<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores;

use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaTransformer;
use App\Services\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaService;
use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;

class EmpresaBoletinadaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var EmpresaBoletinadaService
     */
    protected $service;

    /**
     * @var EmpresaBoletinadaTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param EmpresaBoletinadaService $service
     * @param EmpresaBoletinadaTransformer $transformer
     */
    public function __construct(Manager $fractal, EmpresaBoletinadaService $service, EmpresaBoletinadaTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->middleware('permiso:eliminar_empresa_boletinada')->only(['destroy']);
        $this->middleware('permiso:editar_empresa_boletinada')->only(['update']);
        $this->middleware('permiso:registrar_empresa_boletinada')->only(['store']);


        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

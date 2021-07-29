<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores;

use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivoTransformer;
use App\Services\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivoService;
use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;

use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\InvitacionTransformer;
use App\Services\SEGURIDAD_ERP\PadronProveedores\InvitacionService;

class InvitacionArchivoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var
     */
    protected $service;

    /**
     * @var
     */
    protected $transformer;

    /**
     * InvitacionController constructor.
     * @param Manager $fractal
     * @param InvitacionService $service
     * @param InvitacionTransformer $transformer
     */
    public function __construct(Manager $fractal, InvitacionArchivoService $service, InvitacionArchivoTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function documento($id){
        return $this->service->documento($id);
    }

    public function descargar($id){
        return $this->service->descargar($id);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 02:15 PM
 */

namespace App\Http\Controllers\v1\CADECO\PortalProveedor;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\PortalProveedor\SolicitudAutorizacionAvanceTransformer;
use App\Services\CADECO\PortalProveedor\SolicitudAutorizacionAvanceService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class SolicitudAutorizacionAvanceController extends Controller
{
    use ControllerTrait;

    /**
     * @var SolicitudAutorizacionAvanceService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SolicitudAutorizacionAvanceTransformer
     */
    protected $transformer;

    /**
     * SolicitudAutorizacionAvanceController constructor.
     * @param SolicitudAutorizacionAvanceService $service
     * @param Manager $fractal
     * @param SolicitudAutorizacionAvanceTransformer $transformer
     */
    public function __construct(SolicitudAutorizacionAvanceService $service, Manager $fractal, SolicitudAutorizacionAvanceTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->middleware('permisoGlobal:consultar_solicitud_autorizacion_avance_proveedor')->only(['proveedorConceptos','index']);
        $this->middleware('permisoGlobal:registrar_solicitud_autorizacion_avance_proveedor')->only('store');
        $this->middleware('permisoGlobal:editar_solicitud_autorizacion_avance_proveedor')->only('update');
        $this->middleware('permisoGlobal:eliminar_solicitud_autorizacion_avance_proveedor')->only('destroy');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function index()
    {
        return $this->service->index();
    }

    public function store(Request $request)
    {
        return $this->service->store($request->all());
    }

    public function proveedorConceptos(Request $request, $id)
    {
        return $this->service->proveedorConceptos($id, $request->all()['base']);
    }

    public function update(Request $request, $id)
    {
        return $this->service->update($request->all(), $id);
    }

    public function destroy(Request $request, $id)
    {
        return $this->service->delete($request->all(), $id);
    }

    public function pdfSolicitudAvanceFormato(Request $request, $id)
    {
        return $this->service->pdfSolicitudAvanceFormato($id, $request->all())->create();
    }

    public function registrarRetencionIva(Request $request, $id)
    {
        return $this->service->registrarRetencionIva($request->all(), $id);
    }
}

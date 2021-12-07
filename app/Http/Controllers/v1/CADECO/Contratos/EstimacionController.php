<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 02:15 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contratos;


use App\Http\Controllers\Controller;
use App\Http\Requests\AprobarEstimacionRequest;
use App\Http\Requests\DeleteEstimacionRequest;
use App\Http\Requests\RevertirAprobacionEstimacionRequest;
use App\Http\Requests\StoreEstimacionRequest;
use App\Http\Transformers\CADECO\Contrato\EstimacionTransformer;
use App\Services\CADECO\EstimacionService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;

class EstimacionController extends Controller
{
    use ControllerTrait {
        store as protected traitStore;
        destroy as protected traitDestroy;
    }

    /**
     * @var EstimacionService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var EstimacionTransformer
     */
    protected $transformer;

    /**
     * EstimacionController constructor.
     * @param EstimacionService $service
     * @param Manager $fractal
     * @param EstimacionTransformer $transformer
     */
    public function __construct(EstimacionService $service, Manager $fractal, EstimacionTransformer $transformer)
    {

        $this->middleware('auth:api');
        $this->middleware('context')->except(['indexProveedor','storeProveedor','proveedorConceptos','updateProveedor','destroyProveedor', 'pdfSolicitudAvanceFormato']);

        $this->middleware('permiso:consultar_formato_orden_pago_estimacion')->only('pdfOrdenPago');
        $this->middleware('permiso:registrar_estimacion_subcontrato')->only('store');
        $this->middleware('permiso:aprobar_estimacion_subcontrato')->only('aprobar');
        $this->middleware('permiso:revertir_aprobacion_estimacion_subcontrato')->only('revertirAprobacion');
        $this->middleware('permiso:eliminar_estimacion_subcontrato')->only('destroy');
        $this->middleware('permiso:actualizar_amortizacion_anticipo')->only('anticipo');
        $this->middleware('permisoGlobal:consultar_estimacion_proveedor')->only(['proveedorConceptos','indexProveedor']);
        $this->middleware('permisoGlobal:registrar_estimacion_proveedor')->only('storeProveedor');
        $this->middleware('permisoGlobal:editar_estimacion_proveedor')->only('updateProveedor');
        $this->middleware('permisoGlobal:eliminar_estimacion_proveedor')->only('destroyProveedor');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function pdfOrdenPago($id)
    {
        return $this->service->pdfOrdenPago($id)->create();
    }

    public function store(StoreEstimacionRequest $request)
    {
        return $this->traitStore($request);
    }

    /**
     * @param AprobarEstimacionRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function aprobar(AprobarEstimacionRequest $request, $id)
    {
        $estimacion = $this->service->aprobar($id);
        return $this->respondWithItem($estimacion);
    }

    public function revertirAprobacion(RevertirAprobacionEstimacionRequest $request, $id)
    {
        $estimacion = $this->service->revertirAprobacion($id);
        return $this->respondWithItem($estimacion);
    }
    public function pdfEstimacion($id)
    {
        return $this->service->pdfEstimacion($id)->create();
    }

    public function anticipo(Request $request, $id)
    {
        return $this->service->anticipo($request->all(), $id);
    }

    public function destroy(DeleteEstimacionRequest $request, $id)
    {
        return $this->traitDestroy($request, $id);
    }

    public function registrarRetencionIva(Request $request, $id)
    {
        return $this->service->registrarRetencionIva($request->all(), $id);
    }

    public function ordenarConceptos($id)
    {
        return $this->service->ordenado($id);
    }

    public function indexProveedor()
    {
        return $this->service->indexProveedor();
    }

    public function storeProveedor(Request $request)
    {
        return $this->service->storeProveedor($request->all());
    }

    public function proveedorConceptos(Request $request, $id)
    {
        return $this->service->proveedorConceptos($id, $request->all()['base']);
    }

    public function updateProveedor(Request $request, $id)
    {
        return $this->service->updateProveedor($request->all(), $id);
    }

    public function destroyProveedor(Request $request, $id)
    {
        return $this->service->deleteProveedor($request->all(), $id);
    }

    public function pdfSolicitudAvanceFormato(Request $request, $id){
        return $this->service->pdfSolicitudAvanceFormato($id, $request->all())->create();
    }
}

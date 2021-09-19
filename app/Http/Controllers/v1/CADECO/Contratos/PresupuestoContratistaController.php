<?php


namespace App\Http\Controllers\v1\CADECO\Contratos;

use App\Http\Controllers\Controller;
use App\Http\Requests\EliminarPresupuestoContratistaRequest;
use App\Http\Transformers\CADECO\Contrato\PresupuestoContratistaTransformer;
use App\Services\CADECO\Contratos\PresupuestoContratistaService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class PresupuestoContratistaController extends Controller
{
    use ControllerTrait {
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var PresupuestoContratistaService
     */
    private $service;

    /**
     * @var PresupuestoContratistaTransformer
     */
    private $transformer;

    /**
     * PresupuestoContratistaController constructor
     * @param Manager $fractal
     * @param PresupuestoContratistaService $service
     * @param PresupuestoContratistaTransformer $transformer
     */

     public function __construct(Manager $fractal, PresupuestoContratistaService $service, PresupuestoContratistaTransformer $transformer)
     {
         $this->middleware('auth:api');
         $this->middleware('context')->except(['registrarPresupuestoProveedor','updatePortalProveedor','descargaLayoutProveedor','cargaLayoutProveedor','destroyProveedor','enviar']);
         $this->middleware('permiso:consultar_presupuesto_contratista')->only(['show','paginate','index','find', 'pdf']);
         $this->middleware('permiso:editar_presupuesto_contratista')->only('update');
         $this->middleware('permiso:eliminar_presupuesto_contratista')->only('destroy');
         $this->middleware('permiso:registrar_presupuesto_contratista')->only(['store']);
         $this->middleware('permiso:descargar_layout_presupuesto_contratista')->only(['descargaLayout']);
         $this->middleware('permiso:cargar_layout_presupuesto_contratista')->only(['cargaLayout']);
         $this->middleware('permisoGlobal:registrar_cotizacion_proveedor')->only(['registrarPresupuestoProveedor']);
         $this->middleware('permisoGlobal:editar_cotizacion_proveedor')->only(['updatePortalProveedor']);
         $this->middleware('permisoGlobal:descargar_layout_cotizacion_proveedor')->only(['descargaLayoutProveedor']);
         $this->middleware('permisoGlobal:cargar_layout_cotizacion_proveedor')->only(['cargaLayoutProveedor']);
         $this->middleware('permisoGlobal:eliminar_cotizacion_proveedor')->only(['destroyProveedor']);

         $this->fractal = $fractal;
         $this->service = $service;
         $this->transformer = $transformer;
     }

     public function destroy(EliminarPresupuestoContratistaRequest $request, $id)
     {
         return $this->traitDestroy($request, $id);
     }

     public function descargaLayout($id)
     {
         return $this->service->descargaLayout($id);
     }

     public function cargaLayout(Request $request)
     {
         $res = $this->service->cargaLayout($request->file, $request->id, $request->name);
         return response()->json($res, 200);
     }

    public function pdf($id)
    {
        if(auth()->user()->can('consultar_presupuesto_contratista')) {
            return $this->service->pdf($id)->create();
        }
        dd( 'No cuentas con los permisos necesarios para realizar la acción solicitada');
    }

    public function registrarPresupuestoProveedor(Request $request){
        $item = $this->service->storePortalProveedor($request->all());
        return $this->respondWithItem($item);
    }

    public function updatePortalProveedor(Request $request, $id)
    {
        $item = $this->service->updatePortalProveedor($request->all(), $id);
        return $this->respondWithItem($item);
    }

    public function descargaLayoutProveedor(Request $request, $id)
    {
        return $this->service->descargaLayoutProveedor($id, $request->all());
    }

    public function cargaLayoutProveedor(Request $request)
    {
        $res = $this->service->cargaLayoutProveedor($request->file, $request->id_invitacion, $request->name, $request->id_presupuesto);
        return response()->json($res, 200);
    }

    public function destroyProveedor(Request $request, $id)
    {
        $this->service->deleteProveedor($request->all(), $id);
        return response()->json("{}", 200);
    }

    public function enviar(Request $request, $id)
    {
        $this->service->enviar($id, $request->all());
        return response()->json([], 200);
    }
}

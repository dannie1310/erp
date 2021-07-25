<?php


namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Compras\CotizacionCompraTransformer;
use App\Services\CADECO\Compras\CotizacionService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class CotizacionController extends Controller
{
    use ControllerTrait {
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CotizacionService
     */
    private $service;

    /**
     * @var CotizacionCompraTransformer
     */
    private $transformer;

    /**
     * CotizacionController constructor.
     * @param Manager $fractal
     * @param CotizacionService $service
     * @param CotizacionCompraTransformer $transformer
     */

    public function __construct(Manager $fractal, CotizacionService $service, CotizacionCompraTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context')->except('storePortalProveedor');
        $this->middleware('permiso:registrar_cotizacion_compra')->only(['store']);
        $this->middleware('permiso:cargar_layout_cotizacion_compra')->only(['cargaLayout']);
        $this->middleware('permiso:descargar_layout_cotizacion_compra')->only(['descargaLayout']);
        $this->middleware('permiso:consultar_cotizacion_compra')->only(['show','paginate','index','find']);
        $this->middleware('permiso:editar_cotizacion_compra')->only(['update']);
        $this->middleware('permiso:eliminar_cotizacion_compra')->only(['destroy']);
        $this->middleware('permisoGlobal:registrar_cotizacion_proveedor')->only(['storePortalProveedor']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
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
        if(auth()->user()->can('consultar_cotizacion_compra')) {
            return $this->service->pdf($id)->create();
        }
        dd( 'No cuentas con los permisos necesarios para realizar la acción solicitada');
    }

    public function storePortalProveedor(Request $request)
    {
        $item = $this->service->storePortalProveedor($request->all());
        return $this->respondWithItem($item);
    }
}

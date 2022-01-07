<?php


namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Compras\SolicitudCompraTransformer;
use App\Services\CADECO\Compras\SolicitudCompraService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class SolicitudCompraController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SolicitudCompraService
     */
    protected $service;

    /**
     * @var SolicitudCompraTransformer
     */
    protected $transformer;

    /**
     * SolicitudCompraController constructor.
     * @param Manager $fractal
     * @param SolicitudCompraService $service
     * @param SolicitudCompraTransformer $transformer
     */
    public function __construct(Manager $fractal, SolicitudCompraService $service, SolicitudCompraTransformer $transformer)
    {
        $this->middleware('addAccessToken')->only('pdfSolicitudCompra');
        $this->middleware('auth:api')->except('leerQR');
        $this->middleware('context')->except('leerQR');

        $this->middleware('permiso:registrar_solicitud_compra')->only('store');
        $this->middleware('permiso:consultar_solicitud_compra')->only(['paginate', 'show']);
        $this->middleware('permiso:aprobar_solicitud_compra')->only('aprobar');
        $this->middleware('permiso:eliminar_solicitud_compra')->only('destroy');
        $this->middleware('permiso:editar_solicitud_compra')->only('update');
        $this->middleware('permiso:consultar_asignacion_proveedor')->only('getCotizaciones');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function pdfSolicitudCompra($id)
    {
        return $this->service->pdfSolicitudCompra($id);
    }

    public function pdfComparativaCotizaciones($id)
    {
        if(auth()->user()->can('consultar_cotizacion_compra')) {
            return $this->service->pdfComparativaCotizaciones($id)->create();
        }
        dd( 'No cuentas con los permisos necesarios para realizar la acción solicitada');
    }

    public function getComparativaCotizaciones(Request $request, $id)
    {
        return $this->service->getComparativaCotizaciones($request->all(),$id);
    }

    public function aprobar(Request $request, $id)
    {
        return $this->service->aprobar($request->all(), $id);
    }

    public function getCotizaciones($id)
    {
        return $this->service->getCotizaciones($id);
    }

    public function leerQR(Request $request)
    {
        return $this->service->leerQR($request->all()['data']);
    }

    public function getCuerpoCorreo($id)
    {
        return $this->service->getCuerpoCorreo($id);
    }

    public function descargaLayoutAsignacion($id){
        return $this->service->getLayoutAsignacion($id);
    }
}

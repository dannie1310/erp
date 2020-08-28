<?php


namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Requests\Compras\EliminarAsignacionRequest;
use App\Services\CADECO\Compras\AsignacionProveedorService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use App\Http\Transformers\CADECO\Compras\AsignacionProveedorTransformer;

class AsignacionProveedorController extends Controller
{
    use ControllerTrait {
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var AsignacionProveedorService
     */

    private $service;

    /**
     * @var AsignacionProveedorTransformer
     */
    private $transformer;

    /**
     * AsignacionProveedorController constructor.
     * @param Manager $fractal
     * @param AsignacionProveedorService $service
     * @param AsignacionProveedorTransformer $transformer
     */

    public function __construct(Manager $fractal, AsignacionProveedorService $service, AsignacionProveedorTransformer $transformer)
    {
        $this->middleware('addAccessToken')->only('pdf');
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:registrar_asignacion_proveedor')->only('store');
        $this->middleware('permiso:consultar_asignacion_proveedor')->only(['paginate', 'show']);
        $this->middleware('permiso:eliminar_asignacion_proveedor')->only('destroy');
        $this->middleware('permiso:registrar_orden_compra')->only('generarOrdenCompra');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function cargaLayout(Request $request){
        $respuesta = $this->service->cargaLayout($request->file);
        return response()->json($respuesta, 200);
    }

    public function destroy(EliminarAsignacionRequest $request, $id)
    {
        return $this->traitDestroy($request, $id);
    }

    public function generarOrdenCompra(Request $request){
        return $this->service->generarOrdenCompra($request->all());
    }

    public function generarOrdenIndividual(Request $request){
        return $this->service->generarOrdenIndividual($request->all());
    }

    public function getAsignacion($id){
        return $this->service->getAsignacion($id);
    }

    public function pendientesOrden(){
        return $this->respondWithCollection($this->service->pendientesOrden());
    }

    public function descargaLayout($id)
    {
//        Falta descarga
        var_dump('Descarga de layout de controlador asignacion',$id);
    }

    public function pdf($id)
    {
        return $this->service->pdf($id);
    }
}

<?php


namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Requests\Compras\EliminarAsignacionRequest;
use App\Services\CADECO\Compras\AsignacionService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use App\Http\Transformers\CADECO\Compras\AsignacionProveedoresTransformer;

class AsignacionController extends Controller
{
    use ControllerTrait {
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var AsignacionService
     */

    private $service;

    /**
     * @var AsignacionProveedoresTransformer
     */
    private $transformer;

    /**
     * AsignacionController constructor.
     * @param Manager $fractal
     * @param AsignacionService $service
     * @param AsignacionProveedoresTransformer $transformer
     */

    public function __construct(Manager $fractal, AsignacionService $service, AsignacionProveedoresTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:registrar_asignacion_proveedor')->only('store');
        $this->middleware('permiso:consultar_asignacion_proveedor')->only(['paginate', 'show']);
        $this->middleware('permiso:eliminar_asignacion_proveedor')->only('destroy');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function asignacion($id)
    {
        $this->service->asignacion($id)->create();
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

    public function getAsignacion($id){
        return $this->service->getAsignacion($id);
    }

    public function descargaLayout($id)
    {
//        Falta descarga
        var_dump('Descarga de layout de controlador asignacion',$id);
    }
}

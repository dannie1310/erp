<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\SEGURIDAD_ERP\PadronProveedores\ArchivoService;
use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\ArchivoTransformer;

class ArchivoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ArchivoService
     */
    protected $service;

    /**
     * @var ArchivoTransformer
     */
    protected $transformer;

    /**
     * GiroController constructor.
     * @param Manager $fractal
     * @param Service $service
     * @param Transformer $transformer
     */
    public function __construct(Manager $fractal, ArchivoService $service, ArchivoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('permisoGlobal:actualizar_expediente_proveedor')->only('cargarArchivo');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function cargarArchivo(Request $request){
        $archivo = $this->service->cargarArchivo($request->all());
        return $this->respondWithItem($archivo);
    }

    public function documento(Request $request,$id){
        return $this->service->documento($request,$id);
    }

    public function getArchivosPrestadora(Request $request)
    {
        $archivos_prestadora = $this->service->getArchivosPrestadora($request);
        return $this->respondWithCollection($archivos_prestadora);
    }

    public function destroy(Request $request, $id)
    {
        $item = $this->service->delete($request->all(), $id);
        return $this->respondWithItem($item);
    }
}

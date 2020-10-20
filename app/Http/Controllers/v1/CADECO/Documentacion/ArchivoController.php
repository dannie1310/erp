<?php


namespace App\Http\Controllers\v1\CADECO\Documentacion;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\CADECO\Documentacion\ArchivoService as Service;
use App\Http\Transformers\CADECO\Documentacion\ArchivoTransformer as Transformer;

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
    public function __construct(Manager $fractal, Service $service, Transformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:cargar_archivos_transaccion')->only('cargarArchivo');
        $this->middleware('permiso:eliminar_archivos_transaccion')->only('destroy');
        $this->middleware('permiso:consultar_archivos_transaccion')->only(['documento', 'getArchivosTransaccion','imagenes']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function cargarArchivo(Request $request){
        $archivo = $this->service->cargarArchivo($request->all());
        return $this->respondWithItem($archivo);
    }

    public function cargarArchivoZIP(Request $request){
        $archivo = $this->service->cargarArchivoZIP($request->all());
        return $this->respondWithItem($archivo);
    }

    public function documento($id){
        return $this->service->documento($id);
    }

    public function getArchivosTransaccion($id)
    {
        $archivos_prestadora = $this->service->getArchivosTransaccion($id);
        return $this->respondWithCollection($archivos_prestadora);
    }

    /*public function destroy(Request $request, $id)
    {
        $item = $this->service->delete($request->all(), $id);
        return $this->respondWithItem($item);
    }*/

    public function imagenes(Request $request, $id)
    {
        return $this->service->imagenBase64($id);
    }
}

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
        $this->middleware('context')->except(['getArchivosTransaccionSC','getArchivosRelacionadosTransaccionSC','documentoSC', 'cargarArchivoSC','destroySC','descargarSC','showSC','imagenesSC']);
        $this->middleware('permiso:cargar_archivos_transaccion')->only('cargarArchivo');
        $this->middleware('permiso:eliminar_archivos_transaccion')->only('destroy');
        $this->middleware('permiso:consultar_archivos_transaccion')->only(['documento', 'getArchivosTransaccion','imagenes']);
        $this->middleware('permisoGlobal:eliminar_archivos_transaccion_proveedor')->only('destroySC');
        $this->middleware('permisoGlobal:consultar_archivos_transaccion_proveedor')->only(['documentoSC', 'getArchivosTransaccionSC','getArchivosRelacionadosTransaccionSC', 'imagenesSC']);
        $this->middleware('permisoGlobal:cargar_archivos_transaccion')->only('cargarArchivoSC');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function cargarArchivo(Request $request){
        $archivo = $this->service->cargarArchivo($request->all());
        return $this->respondWithItem($archivo);
    }

    public function cargarArchivoSC(Request $request){
        $archivo = $this->service->cargarArchivo($request->all(), 1);
        return $this->respondWithItem($archivo);
    }

    public function cargarArchivoZIP(Request $request){
        $archivo = $this->service->cargarArchivoZIP($request->all());
        return $this->respondWithItem($archivo);
    }

    public function documento(Request $request, $id){

        return $this->service->documento($id, $request->db);
    }

    public function documentoSC(Request $request, $id){

        return $this->service->documento($id, $request->db);
    }

    public function getArchivosTransaccion($id)
    {
        $archivos = $this->service->getArchivosTransaccion($id);
        return $this->respondWithCollection($archivos["archivos"]);
    }

    public function getArchivosTransaccionSC(Request $request,$id)
    {
        $archivos = $this->service->getArchivosTransaccion($id, $request->base_datos);
        return $this->respondWithCollection($archivos["archivos"]);
    }

    public function getArchivosRelacionadosTransaccion(Request $request,$tipo,$id)
    {
        $archivos = $this->service->getArchivosRelacionadosTransaccion($request->all(),$tipo,$id);
        return $this->respondWithCollection($archivos);
    }

    public function getArchivosRelacionadosTransaccionSC($tipo,$id)
    {
        $archivos = $this->service->getArchivosRelacionadosTransaccion($tipo,$id);
        return $this->respondWithCollection($archivos);
    }

    public function imagenes(Request $request, $id)
    {
        return $this->service->imagenBase64($request->all(), $id);
    }

    public function imagenesSC(Request $request, $id)
    {
        return $this->service->imagenBase64($request->all(), $id);
    }

    public function destroy(Request $request, $id)
    {
        $this->service->delete($request->all(), $id);
        return response()->json("{}", 200);
    }

    public function destroySC(Request $request, $id)
    {
        $this->service->delete($request->all(), $id);
        return response()->json("{}", 200);
    }

    public function show(Request $request, $id)
    {
        $item = $this->service->show($request->all(),$id);
        return $this->respondWithItem($item);
    }
    public function showSC(Request $request, $id)
    {
        $item = $this->service->show($request->all(),$id);
        return $this->respondWithItem($item);
    }

    public function descargar(Request $request,$id){
        return $this->service->descargar($request->all(),$id);
    }

    public function descargarSC(Request $request,$id){
        return $this->service->descargar($request->all(),$id);
    }

}

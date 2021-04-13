<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Documentacion;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;

use App\Services\SEGURIDAD_ERP\Documentacion\ArchivoService;
use App\Http\Transformers\SEGURIDAD_ERP\Documentacion\ArchivoTransformer;

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

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function cargarArchivo(Request $request){
        $archivo = $this->service->cargarArchivo($request->all());
        return $this->respondWithItem($archivo);
    }

    public function eliminarArchivo(Request $request){
        $archivo = $this->service->eliminarArchivo($request->all());
        return $this->respondWithItem($archivo);
    }

    public function agregarArchivo(Request $request){
        $archivo = $this->service->agregarArchivo($request->all());
        return $this->respondWithItem($archivo);
    }

    public function agregarTipoArchivo(Request $request){
        $archivo = $this->service->agregarTipoArchivo($request->all());
        return $this->respondWithItem($archivo);
    }

    public function reemplazarArchivo(Request $request){
        $archivo = $this->service->reemplazarArchivo($request->all());
        return $this->respondWithItem($archivo);
    }


    public function documento($id){
        return $this->service->documento($id);
    }


    public function destroy(Request $request, $id)
    {
        /*$item = $this->service->delete($id);
        return $this->respondWithItem($item);*/
        $this->service->delete($id);
    }

    public function imagenes(Request $request, $id)
    {
        return $this->service->imagenBase64($id);
    }

    public function getArchivosCFDI($id_cfdi)
    {
        $archivos = $this->service->getArchivosCFDI($id_cfdi);
        return $this->respondWithCollection($archivos);
    }
}

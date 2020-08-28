<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\CtgEfosTransformer;
use App\Services\SEGURIDAD_ERP\Finanzas\CtgEfosService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class CtgEfosController extends Controller
{
    use ControllerTrait;

    /**
     * @var CtgEfosService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CtgEfosTransformer
     */
    private $transformer;

    /**
     * CtgEfosLogController constructor
     * @param CtgEfosService $service
     * @param Manager $fractal
     * @param CtgEfosTransformer $transformer
     */
    public function __construct(CtgEfosService $service, Manager $fractal, CtgEfosTransformer $transformer)
    {
        $this->middleware('auth:api');
        /*$this->middleware('context')->except(['paginate','cargaLayout','rfc']);*/

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function cargaLayout(Request $request){
        $respuesta = $this->service->cargaLayout($request->file);
        return response()->json($respuesta, 200);
    }

    public function rfc(Request $request){
        $respuesta = $this->service->rfcApi($request->rfc);
        return response()->json( $respuesta, 200);
    }

    public function obtenerInforme(Request $request)
    {
        $respuesta =$this->service->obtenerInforme();
        return response()->json($respuesta, 200);
    }
    public function obtenerInformeDesglosado(Request $request)
    {
        $respuesta =$this->service->obtenerInformeDesglosado();
        return response()->json($respuesta, 200);
    }

    public function obtenerInformePDF(Request $request)
    {
        return $this->service->obtenerInformePDF()->create();
    }

    public function obtenerInformeDesglosadoPDF(Request $request)
    {
        return $this->service->obtenerInformeDesglosadoPDF()->create();
    }

}

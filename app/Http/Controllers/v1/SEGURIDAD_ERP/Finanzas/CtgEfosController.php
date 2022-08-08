<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas;


use App\Exports\InformeCFDIDesglosado;
use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\CtgEfosTransformer;
use App\Services\SEGURIDAD_ERP\Finanzas\CtgEfosService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use Maatwebsite\Excel\Facades\Excel;

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
        $this->middleware('permiso:consultar_efos_empresa')
            ->only(['getUltimosCambiosEFOSTXT','getUltimasListasEFOSTXT','getCorreccionesPendientesTXT']);

        $this->middleware('permiso:consultar_informe_listado_efos_vs_cfdi_recibidos')
            ->only(['obtenerInformePDF','obtenerInformeDesglosadoPDF']);
        //
        /*$this->middleware('context')->except(['paginate','cargaLayout','rfc']);*/

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function cargaLayout(Request $request)
    {
        $respuesta = $this->service->cargaLayout($request->file);
        return response()->json($respuesta, 200);
    }

    public function procesaURLCSV(){
        $respuesta = $this->service->procesaURLCSV();
        return response()->json($respuesta, 200);
    }

    public function rfc(Request $request){
        $respuesta = $this->service->rfcApi($request->rfc);
        return response()->json( $respuesta, 200);
    }

    public function getUltimosCambiosEFOSTXT()
    {
        return $this->service->getUltimosCambiosEFOSTXT();
    }

    public function getUltimasListasEFOSTXT()
    {
        return $this->service->getUltimasListasEFOSTXT();
    }

    public function getCorreccionesPendientesTXT()
    {
        return $this->service->getCorreccionesPendientesTXT();
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

    public function descargaInformeCFDIDesglosado(Request $request)
    {
        return Excel::download(new InformeCFDIDesglosado(), 'informe_efos_cfdi_desglosado'.date("Ymd_his").'.xlsx');
    }

    public function obtenerInformeDefinitivoPDF(Request $request)
    {
        return $this->service->obtenerInformeDefinitivoPDF()->create();
    }

}

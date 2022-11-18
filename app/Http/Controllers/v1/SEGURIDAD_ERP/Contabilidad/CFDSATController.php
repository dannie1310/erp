<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 04:20 PM
 */

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad;

use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\CFDSATTransformer as Transformer;
use App\Services\SEGURIDAD_ERP\Contabilidad\CFDSATService as Service;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;
use Illuminate\Http\Request;

class CFDSATController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var Service
     */
    protected $service;

    /**
     * @var Transformer
     */
    protected $transformer;

    public function __construct(Manager $fractal, Service $service, Transformer $transformer)
    {
        $this->middleware( 'auth:api');
        $this->middleware('context', ['only' => ['obtenerNumeroEmpresa']]);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function cargaZIP(Request $request)
    {
        $respuesta =$this->service->storeZIPCFD($request->nombre_archivo, $request->archivo_zip);
        return response()->json($respuesta, 200);
    }

    public function procesaDirectorioZIPCFD(Request $request)
    {
        /*$this->service->reprocesaCFDObtenerTipo();
        return response()->json([], 200);*/
        $respuesta =$this->service->procesaDirectorioZIPCFD();
        return response()->json($respuesta, 200);
    }

    public function obtenerInformeEmpresaMes()
    {
        $respuesta =$this->service->obtenerInformeEmpresaMes();
        return response()->json($respuesta, 200);
    }

    public function getContenidoDirectorio(){
        $respuesta =$this->service->getContenidoDirectorio();
        return response()->json($respuesta, 200);
    }

    public function obtenerInformeCompleto()
    {
        $respuesta =$this->service->obtenerInformeCompleto();
        return response()->json($respuesta, 200);
    }

    public function obtenerInformeCompletoPDF()
    {
        $this->service->obtenerInformeCompletoPDF()->create();
    }

    public function descargar(Request $request){
        return $this->service->descargar($request->all());
    }

    public function descargarIndividual(Request $request, $id){
        return $this->service->descargarIndividual($id);
    }

    public function pdfCFDI($id)
    {
        return $this->service->pdfCFDI($id)->create();
    }

    public function cargaXMLProveedor(Request $request)
    {
        $item = $this->service->cargaXMLProveedor($request->all());
        return $this->respondWithItem($item);
    }

    public function obtenerInformeSATLP2020(Request $request)
    {
        $respuesta =$this->service->obtenerInformeSATLP2020($request->all());
        return response()->json($respuesta, 200);
    }

    public function obtenerInformeCostosCFDIvsCostosBalanza(Request $request)
    {
        $respuesta =$this->service->obtenerInformeCostosCFDIvsCostosBalanza($request->all());
        return response()->json($respuesta, 200);

    }

    public function obtenerCuentasInformeSATLP2020(Request $request, $id)
    {
        $respuesta =$this->service->obtenerCuentasInformeSATLP2020($request->all());
        return response()->json($respuesta, 200);
    }

    public function obtenerMovimientosCuentasInformeSATLP2020(Request $request, $id)
    {
        $respuesta =$this->service->obtenerMovimientosCuentasInformeSATLP2020($request->all());
        return response()->json($respuesta, 200);
    }

    public function obtenerListaCFDI(Request $request, $id)
    {
        $respuesta =$this->service->obtenerListaCFDI($request->all());
        return response()->json($respuesta, 200);
    }

    public function obtenerListaCFDIMesAnio(Request $request, $id)
    {
        $respuesta =$this->service->obtenerListaCFDIMesAnio($request->all());
        return response()->json($respuesta, 200);
    }

    public function obtenerNumeroEmpresa()
    {
        $respuesta =$this->service->obtenerNumeroEmpresa();
        return response()->json($respuesta, 200);
    }

    public function obtenerListaCFDICostosCFDICostosBalanza(Request $request, $id)
    {
        $respuesta =$this->service->obtenerListaCFDICostosCFDIBalanza($request->all());
        return response()->json($respuesta, 200);
    }

    public function descargaLayout(Request $request)
    {
        return $this->service->descargaExcel($request->all());
    }

    public function descargarComunicados(Request $request){
        return $this->service->descargarComunicados($request->all());
    }
}

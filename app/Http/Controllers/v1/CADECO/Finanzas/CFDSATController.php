<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 04:20 PM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


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
        $this->middleware('context');
        $this->middleware('permiso:consultar_cfdi')->only(['paginate','descargar','pdfCFDI']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function descargar(Request $request){
        return $this->service->descargar($request->all());
    }

    public function pdfCFDI($id)
    {
        return $this->service->pdfCFDI($id)->create();
    }

    /**
     * Descargar Layout con los CFDISAT
     * @return mixed
     */
    public function descargaLayout(Request $request)
    {
        return $this->service->descargaExcel($request->all());
    }

    public function cargaXMLComprobacion(Request $request)
    {
        $conceptos = $this->service->cargaXMLComprobacion($request->all());
        return response()->json($conceptos, 200);
    }

    public function descargaCFDIREPPendienteXLS(Request $request)
    {
        return $this->service->descargaExcelCFDIRepPendinete($request->all());
    }
}

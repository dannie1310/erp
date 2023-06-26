<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:46 AM
 */

namespace App\Http\Controllers\v1\CTPQ;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CTPQ\PolizaTransformer;
use App\Services\CTPQ\PolizaService;
use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;

class PolizaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var PolizaService
     */
    protected $service;

    /**
     * @var PolizaTransformer
     */
    protected $transformer;

    /**
     * PolizaController constructor.
     *
     * @param Manager $fractal
     * @param PolizaService $service
     * @param PolizaTransformer $transformer
     */
    public function __construct(Manager $fractal, PolizaService $service, PolizaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('permisoGlobal:consultar_poliza_ctpq')->only(['show','pdf','pdfCaidaB','descargaZip']);
        $this->middleware('permisoGlobal:editar_poliza_ctpq')->only('update');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function show(Request $request, $id)
    {
        $item = $this->service->show($request->all(), $id);
        return $this->respondWithItem($item);
    }

    public function pdf(Request $request,$id)
    {
        return $this->service->pdf($request,$id);
    }

    public function pdfCaidaB(Request $request, $id)
    {
        return $this->service->pdfCaidaB($request->all(), $id);
    }

    public function descargaZip(Request $request){
        return $this->service->descargaZip($request->all());
    }

    public function busquedaExcel(Request $request)
    {
        return $this->service->busquedaExcel($request->all());
    }

    public function getZip(Request $request){
        return $this->service->getZip($request->all());
    }

    public function getAsociacionCFDI()
    {
        $respuesta =$this->service->asociarCFDI();
        return response()->json($respuesta, 200);
    }

    public function listarPosiblesCFDI(Request $request)
    {
        return $this->service->listarPosiblesCFDI($request->all());
    }
}

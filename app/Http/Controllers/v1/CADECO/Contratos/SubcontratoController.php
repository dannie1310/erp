<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 25/02/2019
 * Time: 07:05 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contratos;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contrato\SubcontratoTransformer;
use App\Services\CADECO\Contratos\SubcontratoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;

class SubcontratoController extends Controller
{
    use ControllerTrait;

    /**
     * @var SubcontratoService
     */
    private $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SubcontratoTransformer
     */
    protected $transformer;

    /**
     * SubcontratoController constructor.
     * @param SubcontratoService $service
     * @param Manager $fractal
     * @param SubcontratoTransformer $transformer
     */
    public function __construct(SubcontratoService $service, Manager $fractal, SubcontratoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context')->except(['indexSinContexto','showSinContexto','ordenarConceptosProveedor']);
        $this->middleware('permiso:consultar_subcontrato')->only(['show', 'paginate', 'pdf']);
        $this->middleware('permiso:eliminar_subcontrato')->only('destroy');
        $this->middleware('permiso:editar_subcontrato')->only(['updateContrato']);
        $this->middleware('permisoGlobal:registrar_solicitud_autorizacion_avance_proveedor')->only(['indexSinContexto','showSinContexto','ordenarConceptosProveedor']);

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function ordenarConceptos($id)
    {
        return $this->service->ordenado($id);
    }

    public function pdf($id)
    {
        return $this->service->pdf($id);
    }

    public function updateContrato(Request $request, $id){
        $resp = $this->service->updateContrato($request->all(), $id);
        return $this->respondWithItem($resp);
    }

    public function descargarLayoutCambiosPrecioVolumen($id){
        return $this->service->descargarLayoutCambiosPrecioVolumen($id);
    }

    public function indexSinContexto(Request $request)
    {
        return $this->service->indexSinContexto($request->all());
    }

    public function showSinContexto(Request $request, $id)
    {
        return $this->service->showSinContexto($id, $request->all());
    }

    public function ordenarConceptosProveedor(Request $request,$id)
    {
        return $this->service->paraEstimarProveedor($id, $request->all());
    }

    public function ordenarConceptosAvance($id)
    {
        return $this->service->ordenadoAvance($id);
    }
}

<?php


namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Requests\Finanzas\EliminarFacturaRequest;
use App\Http\Transformers\CADECO\Finanzas\FacturaTransformer;
use App\Services\CADECO\Finanzas\FacturaService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;

class FacturaController extends Controller
{
    use ControllerTrait{
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var FacturaService
     */
    protected $service;

    /**
     * @var FacturaTransformer
     */
    protected $transformer;

    /**
     * FacturaController constructor.
     * @param Manager $fractal
     * @param FacturaService $service
     * @param FacturaTransformer $transformer
     */
    public function __construct(Manager $fractal, FacturaService $service, FacturaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_factura')->only(['paginate']);
        $this->middleware('permiso:eliminar_factura')->only(['destroy']);
        $this->middleware('permiso:revertir_revision_factura')->only(['revertir']);
        $this->middleware('permiso:editar_factura')->only(['update']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function autorizadas(){
        $autorizadas = $this->service->autorizadas();
        return response()->json($autorizadas);
    }

    public function destroy(EliminarFacturaRequest $request, $id)
    {
        return $this->traitDestroy($request, $id);
    }

    public function pendientesPago($id){
        $pendientes = $this->service->pendientesPago($id);
        return $this->respondWithPaginator($pendientes);
    }

    public function pdfCR($id)
    {
        return $this->service->pdfCR($id)->create();
    }

    public function cargaXML(Request $request)
    {
        $respuesta = $this->service->cargaXML($request->all());
        return response()->json($respuesta, 200);
    }

    public function revertir($id)
    {
        return $this->respondWithItem($this->service->revertir($id));
    }

    public function pdfCFDI($id)
    {
        return $this->service->pdfCFDI($id)->create();
    }
}

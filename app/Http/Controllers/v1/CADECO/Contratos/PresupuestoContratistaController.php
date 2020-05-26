<?php


namespace App\Http\Controllers\v1\CADECO\Contratos;

use App\Http\Controllers\Controller;
use App\Http\Requests\EliminarPresupuestoContratistaRequest;
use App\Http\Transformers\CADECO\Contrato\PresupuestoContratistaTransformer;
use App\Services\CADECO\Contratos\PresupuestoContratistaService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class PresupuestoContratistaController extends Controller
{
    use ControllerTrait {
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var PresupuestoContratistaService
     */
    private $service;

    /**
     * @var PresupuestoContratistaTransformer
     */
    private $transformer;

    /**
     * PresupuestoContratistaController constructor
     * @param Manager $fractal
     * @param PresupuestoContratistaService $service
     * @param PresupuestoContratistaTransformer $transformer
     */

     public function __construct(Manager $fractal, PresupuestoContratistaService $service, PresupuestoContratistaTransformer $transformer)
     {
         $this->middleware('auth:api');         
         $this->middleware('context');
         $this->middleware('permiso:consultar_presupuesto_contratista')->only(['show','paginate','index','find']);
         $this->middleware('permiso:editar_presupuesto_contratista')->only('update');
         $this->middleware('permiso:eliminar_presupuesto_contratista')->only('destroy');
         $this->middleware('permiso:registrar_presupuesto_contratista')->only(['store']);
         $this->middleware('permiso:descargar_layout_presupuesto_contratista')->only(['descargaLayout']);

         $this->fractal = $fractal;
         $this->service = $service;
         $this->transformer = $transformer;
     }

     public function destroy(EliminarPresupuestoContratistaRequest $request, $id)
     {
         return $this->traitDestroy($request, $id);
     }

     public function descargaLayout($id)
     {
         return $this->service->descargaLayout($id);
     }

     public function cargaLayout(Request $request)
     {
         $res = $this->service->cargaLayout($request->file, $request->id, $request->name);
         return response()->json($res, 200);
     }
}
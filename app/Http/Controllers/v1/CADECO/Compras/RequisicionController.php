<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/11/2019
 * Time: 04:34 PM
 */

namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteRequisicionRequest;
use App\Http\Transformers\CADECO\Compras\RequisicionTransformer;
use App\Services\CADECO\Compras\RequisicionService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class RequisicionController extends Controller
{
    use ControllerTrait {
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var RequisicionTransformer
     */
    private $transformer;

    /**
     * @var RequisicionService
     */
    private $service;

    /**
     * RequisicionController constructor.
     * @param Manager $fractal
     * @param RequisicionTransformer $transformer
     * @param RequisicionService $service
     */
    public function __construct(Manager $fractal, RequisicionTransformer $transformer, RequisicionService $service)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_requisicion_compra')->only(['show', 'paginate', 'index', 'find', 'pdfRequisicion']);
        $this->middleware('permiso:eliminar_requisicion_compra')->only('destroy');
        $this->middleware('permiso:registrar_requisicion_compra')->only('store');

        $this->fractal = $fractal;
        $this->transformer = $transformer;
        $this->service = $service;
    }

    public function cargaLayout(Request $request)
    {
         $res = $this->service->cargaLayout($request->file);
        return response()->json($res, 200);
    }

    public function pdfRequisicion($id)
    {
        return $this->service->pdfRequisicion($id)->create();
    }

    public function destroy(DeleteRequisicionRequest $request, $id)
    {
        return $this->traitDestroy($request, $id);
    }
}

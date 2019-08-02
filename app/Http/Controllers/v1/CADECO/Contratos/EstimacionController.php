<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 02:15 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contratos;


use App\Http\Controllers\Controller;
use App\Http\Requests\AprobarEstimacionRequest;
use App\Http\Requests\RevertirAprobacionEstimacionRequest;
use App\Http\Requests\StoreEstimacionRequest;
use App\Http\Transformers\CADECO\Contrato\EstimacionTransformer;
use App\Services\CADECO\EstimacionService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;

class EstimacionController extends Controller
{
    use ControllerTrait {
        store as protected traitStore;
    }

    /**
     * @var EstimacionService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var EstimacionTransformer
     */
    protected $transformer;

    /**
     * EstimacionController constructor.
     * @param EstimacionService $service
     * @param Manager $fractal
     * @param EstimacionTransformer $transformer
     */
    public function __construct(EstimacionService $service, Manager $fractal, EstimacionTransformer $transformer)
    {

        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_formato_orden_pago_estimacion')->only('pdfOrdenPago');
        $this->middleware('permiso:registrar_estimacion_subcontrato')->only('store');
        $this->middleware('permiso:aprobar_estimacion_subcontrato')->only('aprobar');
        $this->middleware('permiso:revertir_aprobacion_estimacion_subcontrato')->only('revertirAprobacion');

        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function pdfOrdenPago($id)
    {
        return $this->service->pdfOrdenPago($id)->create();
    }

    public function store(StoreEstimacionRequest $request)
    {
        return $this->traitStore($request);
    }

    public function getConceptos(Request $request, $id_estimacion)
    {
        $estimacion = $this->service->show($id_estimacion);
        $conceptos = DB::connection('cadeco')
            ->select(DB::raw("EXEC [SubcontratosEstimaciones].[uspConceptosEstimacion] {$estimacion->id_antecedente}, {$id_estimacion}, 0, 0"));
        return response()->json($conceptos);
    }

    /**
     * @param AprobarEstimacionRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function aprobar(AprobarEstimacionRequest $request, $id)
    {
        $estimacion = $this->service->aprobar($id);
        return $this->respondWithItem($estimacion);
    }

    public function revertirAprobacion(RevertirAprobacionEstimacionRequest $request, $id)
    {
        $estimacion = $this->service->revertirAprobacion($id);
        return $this->respondWithItem($estimacion);
    }
}

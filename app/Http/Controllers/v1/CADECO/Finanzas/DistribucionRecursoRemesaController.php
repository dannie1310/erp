<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 11:34 AM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Requests\Finanzas\StoreDistribucionRecursoRemesaRequest;
use App\Http\Transformers\CADECO\Finanzas\DistribucionRecursoRemesaTransformer;
use App\Services\CADECO\Finanzas\DistribucionRecursoRemesaService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class DistribucionRecursoRemesaController extends Controller
{
    use ControllerTrait{
        store as protected traitStore;
    }

    /**
     * @var DistribucionRecursoRemesaService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var DistribucionRecursoRemesaTransformer
     */
    private $transformer;

    /**
     * DistribucionRecursoRemesaController constructor.
     * @param DistribucionRecursoRemesaService $service
     * @param Manager $fractal
     * @param DistribucionRecursoRemesaTransformer $transformer
     */
    public function __construct(DistribucionRecursoRemesaService $service, Manager $fractal, DistribucionRecursoRemesaTransformer $transformer)
    {
        $this->middleware('addAccessToken')->only('descargaLayout');
        $this->middleware('auth')->only(['descargaLayoutManual']);
        $this->middleware('auth:api')->except(['descargaLayoutManual']);
        $this->middleware('context');

        $this->middleware('permiso:registrar_distribucion_recursos_remesa')->only(['store']);
        $this->middleware('permiso:autorizar_distribucion_recursos_remesa')->only(['autorizar']);
        $this->middleware('permiso:cancelar_distribucion_recursos_remesa')->only(['cancelar']);
        $this->middleware('permiso:descargar_distribucion_recursos_remesa|pagar_distribucion_recursos_remesa')->only(['validar']);

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
    public function autorizar($id){
        return $this->service->autorizar($id);
    }

    public function cancelar(Request $request, $id)
    {
        $item = $this->service->cancelar($id);
        return $this->respondWithItem($item);
    }

    public function cargarLayout(Request $request, $id){
        $respuesta =  $this->service->cargaLayout($request, $id);
        return response()->json($respuesta, 200);
    }

    public function cargarLayoutManual(Request $request, $id){
        $respuesta =  $this->service->cargaLayoutManual($request, $id);
        return response()->json($respuesta, 200);
    }

    public function descargaLayout($id){
        return $this->service->layoutDistribucionRemesa($id);
//        $item = $this->service->layoutDistribucionRemesa($id)->create();
//        return $this->respondWithItem($item);
    }

    public function descargaLayoutManual($id){
        return $this->service->layoutDistribucionRemesaManual($id);
    }

    public function store(StoreDistribucionRecursoRemesaRequest $request)
    {
        return $this->traitStore($request);
    }

    public function validar(Request $request, $id)
    {
        $item = $this->service->show($id);
        return $this->respondWithItem($item);
    }
}

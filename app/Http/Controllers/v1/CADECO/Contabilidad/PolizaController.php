<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 26/12/18
 * Time: 01:48 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePolizaRequest;
use App\Http\Transformers\CADECO\Contabilidad\PolizaTransformer;
use App\Services\CADECO\Contabilidad\PolizaService;
use App\Traits\ControllerTrait;
use Dingo\Api\Http\Request;
use League\Fractal\Manager;

class PolizaController extends Controller
{
    use ControllerTrait {
        update as protected traitUpdate;
    }

    /**
     * @var PolizaService
     */
    private $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var PolizaTransformer
     */
    protected $transformer;

    /**
     * PolizaController constructor.
     * @param PolizaService $service
     * @param Manager $fractal
     * @param PolizaTransformer $transformer
     */
    public function __construct(PolizaService $service, Manager $fractal, PolizaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_prepolizas_generadas')->only(['show','showEdit','paginate','find','index']);
        $this->middleware('permiso:editar_prepolizas_generadas')->only('update');
        $this->middleware('permiso:validar_prepoliza')->only('validar');
        $this->middleware('permiso:omitir_prepoliza_generada')->only('omitir');
        $this->middleware('permiso:asociar_poliza_contpaq_cfdi')->only(['asociarCFDI','getPolizasPorAsociar']);
        $this->middleware("permiso:descargar-cfdi-pendientes-carga-add")->only(["descargarCFDIPorCargar"]);
        $this->middleware("permiso:consultar-cfdi-pendientes-carga-add")->only(["getCFDIPorCargar"]);

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function update(UpdatePolizaRequest $request, $id)
    {
        return $this->traitUpdate($request, $id);
    }

    public function showEdit(Request $request, $id)
    {
        $item = $this->service->show($id);
        if($item->estatus == 2 ||$item->estatus == 3 ||$item->estatus == -3 ||$item->estatus == 1)
            abort(400,"La prepóliza no puede ser editada");
        return $this->respondWithItem($item);
    }

    public function validar(Request $request, $id)
    {
        $item = $this->service->validar($id);
        return $this->respondWithItem($item);
    }

    public function omitir(Request $request, $id)
    {
        $item = $this->service->omitir($id);
        return $this->respondWithItem($item);
    }

    public function asociarCFDI(Request $request)
    {
        $item = $this->service->asociarCFDI($request->all());
        return response()->json("{}", 200);
    }

    public function getPolizasPorAsociar(Request $request)
    {
        return $this->service->getPolizasPorAsociar();
    }

    public function getCFDIPorCargar(Request $request)
    {
        return $this->service->getCFDIPorCargar();
    }

    public function descargarCFDIPorCargar(Request $request)
    {
        return $this->service->descargarCFDIPorCargar();
    }
}

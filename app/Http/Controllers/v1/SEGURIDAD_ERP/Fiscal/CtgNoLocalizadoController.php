<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\SEGURIDAD_ERP\Fiscal\CtgNoLocalizadoService;
use App\Http\Transformers\SEGURIDAD_ERP\Fiscal\CtgNoLocalizadoTransformer;

class CtgNoLocalizadoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var CtgNoLocalizadoService
     */
    protected $service;

    /**
     * @var CtgNoLocalizadoTransformer
     */
    protected $transformer;

    /**
     * NoDeducidoController constructor.
     * @param Manager $fractal
     * @param NoLocalizadoService $service
     * @param NoLocalizadoTransformer $transformer
     */
    public function __construct(Manager $fractal, CtgNoLocalizadoService $service, CtgNoLocalizadoTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function cargarCsv(Request $request){
        return $this->service->cargarLista($request->all());
    }

    public function obtenerInforme(Request $request)
    {
        $respuesta =$this->service->obtenerInforme();
        return response()->json($respuesta, 200);
    }

    public function obtenerInformePDF(Request $request)
    {
        return $this->service->obtenerInformePDF()->create();
    }
}

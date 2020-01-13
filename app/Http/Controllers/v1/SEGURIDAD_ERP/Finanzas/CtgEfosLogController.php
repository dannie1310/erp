<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\CtgEfosLogTransformer;
use App\Services\SEGURIDAD_ERP\Finanzas\CtgEfosLogService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class CtgEfosLogController extends Controller
{
    use ControllerTrait;

    /**
     * @var CtgEfosLogService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CtgEfosLogTransformer
     */
    private $transformer;

    /**
     * CtgEfosLogController constructor
     * @param CtgEfosLogService $service
     * @param Manager $fractal
     * @param CtgEfosLogTransformer $transformer
     */
    public function __construct(CtgEfosLogService $service, Manager $fractal, CtgEfosLogTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function cargaLayout(Request $request){
//        dd('Llega la carga desde ctg efos log', $request->file);
        $respuesta = $this->service->cargaLayout($request->file);
//        return response()->json($respuesta, 200);
    }

}

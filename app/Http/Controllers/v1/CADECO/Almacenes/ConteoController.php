<?php


namespace App\Http\Controllers\v1\CADECO\Almacenes;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Almacenes\ConteoTransformer;
use App\Http\Transformers\CADECO\Almacenes\InventarioFisicoTransformer;
use App\Services\CADECO\Almacenes\ConteoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class ConteoController extends Controller
{
    use ControllerTrait;

    /**
     * @var ConteoService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ConteoTransformer
     */
    protected $transformer;

    public function __construct(ConteoService $service, Manager $fractal, ConteoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_conteos')->only(['paginate','index','show']);
        $this->middleware('permiso:cargar_layout_captura_conteos')->only('cargaLayout');
        $this->middleware('permiso:eliminar_conteos')->only('cancelar');


        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function cargaLayout(Request $request){
        $respuesta = $this->service->cargaLayout($request->file);
        return response()->json($respuesta, 200);
    }

    public function cancelar(Request $request,$id){
        $respuesta = $this->service->cancelar($request->all(), $id);
        return response()->json($respuesta, 200);
    }

}
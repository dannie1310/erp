<?php


namespace App\Http\Controllers\v1\CADECO\Almacenes;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Almacenes\InventarioFisicoTransformer;
use App\Services\CADECO\Almacenes\ConteoService;
use App\Services\CADECO\Almacenes\InventarioFisicoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class ConteoController extends Controller
{
    use ControllerTrait;

    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var InventarioFisicoTransformer
     */
    protected $transformer;

    public function __construct(ConteoService $service, Manager $fractal, InventarioFisicoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
//        $this->middleware('permiso:consultar_inventario_fisico')->only('paginate');
//        $this->middleware('permiso:iniciar_inventario_fisico')->only('store');


        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function cargaLayout(Request $request){
        $respuesta = $this->service->cargaLayout($request->file);
        return response()->json($respuesta, 200);
    }

}
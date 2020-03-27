<?php


namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Compras\AsignacionTransformer;
use App\Services\CADECO\Compras\AsignacionService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class AsignacionController extends Controller
{
    use ControllerTrait {
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var AsignacionService
     */

    private $service;

    /**
     * @var AsignacionTransformer
     */
    private $transformer;

    /**
     * AsignacionController constructor.
     * @param Manager $fractal
     * @param AsignacionService $service
     * @param AsignacionTransformer $transformer
     */

    public function __construct(Manager $fractal, AsignacionService $service, AsignacionTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function asignacion($id)
    {
        $this->service->asignacion($id)->create();
    }

    public function cargaLayout(Request $request){
        $respuesta = $this->service->cargaLayout($request->file);
        return response()->json($respuesta, 200);
    }

    public function descargaLayout($id)
    {
//        Falta descarga
        var_dump('Descarga de layout de controlador asignacion',$id);
    }
}

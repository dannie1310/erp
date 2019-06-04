<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP;

use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\TipoAreaSubcontratanteTransformer;
use App\Services\SEGURIDAD_ERP\AreaSubcontratanteService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class AreaSubcontratanteController extends Controller
{
    use ControllerTrait {
        store as traitStore;
        destroy as traitDestroy;
    }
    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var AreaSubcontratanteService
     */
    protected $service;

    /**
     * @var TipoAreaSubcontratanteTransformer
     */
    protected $transformer;

    public function __construct(Manager $fractal, AreaSubcontratanteService $service, TipoAreaSubcontratanteTransformer $transformer){

        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function store(Request $request)
    {
        return $this->traitStore($request);
    }

    public function porUsuario(Request $request, $user_id)
    {
        $areas = $this->service->porUsuario($request->all(), $user_id);
        return $this->respondWithCollection($areas);
    }

    public function asignacionAreas(Request $request){
        $response = $this->service->asignacionAreas($request->all());
        return response()->json($response, 200);
    }

}
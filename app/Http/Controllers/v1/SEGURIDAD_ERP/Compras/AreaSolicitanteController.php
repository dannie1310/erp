<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Compras\TipoAreaSolicitanteTransformer;
use App\Services\SEGURIDAD_ERP\Compras\AreaSolicitanteService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class AreaSolicitanteController extends Controller
{
    use ControllerTrait{
        store as traitStore;
        destroy as traitDestroy;
    }

    /**
     * @var AreaSolicitanteService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TipoAreaSolicitanteTransformer
     */
    protected $transformer;

    /**
     * AreaSolicitanteController constructor.
     * @param  Manager $fractal
     * @param AreaSolicitanteService $service
     * @param TipoAreaSolicitanteTransformer $transformer
     */
    public function __construct( Manager $fractal, AreaSolicitanteService $service, TipoAreaSolicitanteTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function asignar(Request $request)
    {
        $response = $this->service->asignar($request->all());
        return response()->json($response, 200);
    }
}

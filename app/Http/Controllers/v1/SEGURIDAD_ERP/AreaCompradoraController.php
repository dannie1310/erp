<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\TipoAreaCompradoraTransformer;
use App\Services\SEGURIDAD_ERP\AreaCompradoraService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class AreaCompradoraController extends Controller
{
    use ControllerTrait{
        store as traitStore;
        destroy as traitDestroy;
    }
    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var AreaCompradoraService
     */
    protected $service;

    /**
     * @var TipoAreaCompradoraTransformer
     */
    protected $transformer;

    /**
     * AreaCompradoraController constructor.
     * @param Manager $fractal
     * @param AreaCompradoraService $service
     * @param TipoAreaCompradoraTransformer $transformer
     */
    public function __construct(Manager $fractal, AreaCompradoraService $service,TipoAreaCompradoraTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }




    public function asignar(Request $request){

        $response = $this->service->asignar($request->all());
        return response()->json($response, 200);
    }
}

<?php


namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\FacturaTransformer;
use App\Services\CADECO\Finanzas\FacturaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class FacturaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var FacturaService
     */
    protected $service;

    /**
     * @var FacturaTransformer
     */
    protected $transformer;

    /**
     * FacturaController constructor.
     * @param Manager $fractal
     * @param FacturaService $service
     * @param FacturaTransformer $transformer
     */
    public function __construct(Manager $fractal, FacturaService $service, FacturaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function autorizadas(){
        $autorizadas = $this->service->autorizadas();
        return response()->json($autorizadas);
    }

    public function pendientesPago($id){
        $pendientes = $this->service->pendientesPago($id);
        return $this->respondWithPaginator($pendientes);
    }


}

<?php


namespace App\Http\Controllers\v1\CADECO\Contratos;


use App\Http\Controllers\Controller;
use App\Http\Requests\Subcontratos\ShowContratoProyectadoRequest;
use App\Http\Transformers\CADECO\Contrato\ContratoProyectadoTransformer;
use App\Services\CADECO\Contratos\ContratoProyectadoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;


class ContratoProyectadoController extends Controller
{

    use ControllerTrait {
        paginate as protected traitPaginate;
        index as protected traitIndex;
        show as protected traitShow;
    }
    /**
     * @var ContratoProyectadoService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ContratoProyectadoTransformer
     */
    protected $transformer;

    /**
     * EstimacionController constructor.
     * @param ContratoProyectadoService $service
     * @param Manager $fractal
     * @param ContratoProyectadoTransformer $transformer
     */

    public function __construct(ContratoProyectadoService $service, Manager $fractal, ContratoProyectadoTransformer $transformer)
    {
//        $this->middleware('auth');
//        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
    public function show(Request $request, $id)
    {
        return $this->traitShow($request, $id);
    }

    public function paginate(Request $request){
        return $this->traitPaginate($request);
    }

    public function index(Request $request)
    {
        return $this->traitIndex($request);
    }

}
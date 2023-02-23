<?php


namespace App\Http\Controllers\v1\CONCURSOS;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CONCURSOS\ConcursoTransformer;
use App\Services\CONCURSOS\ConcursoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class ConcursoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ConcursoService Service
     */
    protected $service;

    /**
     * @var TagTransformer
     */
    protected $transformer;

    /**
     * TagController constructor.
     * @param Manager $fractal
     * @param ConcursoService $service
     * @param ConcursoTransformer $transformer
     */
    public function __construct(Manager $fractal, ConcursoService $service, ConcursoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('permisoGlobal:registrar_concurso')->only('store');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function store(Request $request)
    {
         $item = $this->service->store($request->all());
        return $this->respondWithItem($item);
    }
}

<?php


namespace App\Http\Controllers\v1\CONCURSOS;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CONCURSOS\ConcursoParticipanteTransformer;
use App\Models\SEGURIDAD_ERP\Concursos\ConcursoParticipante;
use App\Services\CONCURSOS\ConcursoParticipanteService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class ConcursoParticipanteController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ConcursoParticipanteService
     */
    protected $service;

    /**
     * @var ConcursoParticipanteTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param ConcursoParticipanteService $service
     * @param ConcursoParticipanteTransformer $transformer
     */
    public function __construct(Manager $fractal, ConcursoParticipanteService $service, ConcursoParticipanteTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->middleware('permisoGlobal:consultar_concurso')->only(['show', 'paginate']);
        $this->middleware('permisoGlobal:editar_concurso')->only('store');
        $this->middleware('permisoGlobal:editar_concurso')->only('update');
        $this->middleware('permisoGlobal:editar_concurso')->only('destroy');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
    public function store(Request $request)
    {
        $participante = $this->service->store($request->all());
        return $this->respondWithItem($participante->concurso);
    }

    public function updateParticipante(Request $request, $id, $id_participante)
    {
        $servicioParticipante = new ConcursoParticipanteService(new ConcursoParticipante());
        $servicioParticipante->update($request->all(), $id_participante);

        return $this->respondWithItem($this->service->show($id));
    }

    public function eliminaParticipante($id, $id_participante)
    {
        $this->service->eliminaParticipante($id, $id_participante);
        return $this->respondWithItem($this->service->show($id));
    }

    public function showParticipante($id, $id_participante)
    {
        $servicioParticipante = new ConcursoParticipanteService(new ConcursoParticipante());
        return $servicioParticipante->show($id_participante);
    }

}

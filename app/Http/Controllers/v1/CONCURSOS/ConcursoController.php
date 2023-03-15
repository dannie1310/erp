<?php


namespace App\Http\Controllers\v1\CONCURSOS;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CONCURSOS\ConcursoTransformer;
use App\Models\SEGURIDAD_ERP\Concursos\ConcursoParticipante;
use App\Services\CONCURSOS\ConcursoParticipanteService;
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

        $this->middleware('permisoGlobal:consultar_concurso')->only(['show', 'paginate']);
        $this->middleware('permisoGlobal:registrar_concurso')->only('store');
        $this->middleware('permisoGlobal:editar_concurso')->only('update');
        $this->middleware('permisoGlobal:cerrar_concurso')->only('cerrar');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function cerrar($id)
    {
        return $this->respondWithItem($this->service->cerrar($id));
    }

    public function pdf($id)
    {
        return $this->service->pdf($id);
    }

    public function graficaPNG($id)
    {
        header('Content-Type: image/png');
        $image = imagecreatefrompng(public_path('downloads/concursos/graficas/'.$id.".png"));
        return imagepng($image);
    }

    public function storeParticipante(Request $request, $id)
    {
        $servicioParticipante = new ConcursoParticipanteService(new ConcursoParticipante());
        $servicioParticipante->store($request->all());
        return $this->respondWithItem($this->service->show($id));
    }

    public function updateParticipante(Request $request, $id, $id_participante)
    {
        $servicioParticipante = new ConcursoParticipanteService(new ConcursoParticipante());
        $servicioParticipante->update($request->all(), $id_participante);
        return $this->respondWithItem($this->service->show($id));
    }

    public function destroyParticipante($id, $id_participante)
    {
        //$this->service->eliminaParticipante($id, $id_participante);
        $servicioParticipante = new ConcursoParticipanteService(new ConcursoParticipante());
        $servicioParticipante->destroy($id_participante);
        return $this->respondWithItem($this->service->show($id));
    }

    public function showParticipante($id, $id_participante)
    {
        $servicioParticipante = new ConcursoParticipanteService(new ConcursoParticipante());
        return $servicioParticipante->show($id_participante);
    }

}

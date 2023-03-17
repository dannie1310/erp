<?php


namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\CONCURSOS\TagTransformer;
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
        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }


    public function showPDF()
    {
        header('Content-Type: application/pdf');
        return $this->service->pdf();
    }


    public function graficaPNG($id)
    {
        header('Content-Type: image/png');
        $image = imagecreatefrompng(public_path('downloads/concursos/graficas/'.$id.".png"));
        return imagepng($image);
    }

}

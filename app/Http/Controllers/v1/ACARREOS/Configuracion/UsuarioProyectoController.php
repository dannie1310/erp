<?php


namespace App\Http\Controllers\v1\ACARREOS\Configuracion;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\ACARREOS\Configuracion\UsuarioProyectoService;
use App\Http\Transformers\ACARREOS\Configuracion\UsuarioProyectoTransformer;

class UsuarioProyectoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var UsuarioProyectoService
     */
    protected $service;

    /**
     * @var UsuarioProyectoTransformer
     */
    protected $transformer;

    /**
     * CamionController constructor.
     * @param Manager $fractal
     * @param UsuarioProyectoService $service
     * @param UsuarioProyectoTransformer $transformer
     */
    public function __construct(Manager $fractal, UsuarioProyectoService $service, UsuarioProyectoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function getChecadores(Request $request){
        $cheadores = $this->service->getChecadores($request->all());
        return $this->respondWithCollection($cheadores);
    }
}
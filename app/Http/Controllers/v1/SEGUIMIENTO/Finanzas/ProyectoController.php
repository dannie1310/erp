<?php


namespace App\Http\Controllers\v1\SEGUIMIENTO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGUIMIENTO\ProyectoTransformer;
use App\Services\SEGUIMIENTO\Finanzas\ProyectoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class ProyectoController extends Controller
{
    use ControllerTrait;

    /**
     * @var ProyectoService
     */
    protected $service;

    /**
     * @var ProyectoTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * ProyectoController constructor.
     * @param ProyectoService $service
     * @param ProyectoTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(ProyectoService $service, ProyectoTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }

    public function index(Request $request)
    {
        return $this->service->index($request->all());
    }
}

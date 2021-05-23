<?php
namespace App\Http\Controllers\v1\MODULOSSAO;


use App\Http\Controllers\Controller;
use App\Services\MODULOSSAO\RemesaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;
use App\Http\Transformers\MODULOSSAO\Proyectos\ProyectoTransformer as Transformer;
use App\Services\MODULOSSAO\Proyectos\ProyectoService as Service;

class ProyectoController extends Controller
{

    use ControllerTrait;

    /**
     * @var RemesaService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var Transformer
     */
    private $transformer;

    /**
     * ProyectoController constructor.
     * @param Service $service
     * @param Manager $fractal
     * @param Transformer $transformer
     */
    public function __construct(Service $service, Manager $fractal, Transformer $transformer)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

}

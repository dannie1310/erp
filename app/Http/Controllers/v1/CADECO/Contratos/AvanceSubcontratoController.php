<?php


namespace App\Http\Controllers\v1\CADECO\Contratos;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contrato\AvanceSubcontratoTransformer;
use App\Services\CADECO\Contratos\AvanceSubcontratoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class AvanceSubcontratoController extends Controller
{
    use ControllerTrait;

    /**
     * @var AvanceSubcontratoService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var AvanceSubcontratoTransformer
     */
    protected $transformer;

    /**
     * AvanceSubcontratoController constructor.
     * @param AvanceSubcontratoService $service
     * @param Manager $fractal
     * @param AvanceSubcontratoTransformer $transformer
     */
    public function __construct(AvanceSubcontratoService $service, Manager $fractal, AvanceSubcontratoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:registrar_avance_subcontrato')->only('store');
        $this->middleware('permiso:consultar_avance_subcontrato')->only(['index', 'paginate', 'show']);

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}

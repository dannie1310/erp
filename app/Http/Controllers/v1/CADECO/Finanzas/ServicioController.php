<?php


namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\ServicioTransformer;
use App\Services\CADECO\Compras\MaterialService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ServicioController extends Controller
{
    use ControllerTrait;

    /**
     * @var MaterialService
     */
    protected $service;

    /**
     * @var ServicioTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * FamiliaController constructor.
     * @param MaterialService $service
     * @param ServicioTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(MaterialService $service, ServicioTransformer $transformer, Manager $fractal)
    {
//        dd('Controlador Servicio');
        $this->middleware('auth:api');
        $this->middleware('context');
//        $this->middleware('permiso:consultar_familia_material,consultar_familia_herramienta_equipo')->only(['show','paginate','index','find']);
//        $this->middleware('permiso:registrar_familia_herramienta_equipo,registrar_familia_material')->only('store');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }

}

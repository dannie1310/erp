<?php


namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\ComprobanteFondoTransformer;
use App\Services\CADECO\Finanzas\ComprobanteFondoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ComprobanteFondoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ComprobanteFondoService
     */
    protected $service;

    /**
     * @var ComprobanteFondoTransformer
     */
    protected $transformer;

    /**
     * ComprobanteFondoController constructor.
     * @param Manager $fractal
     * @param ComprobanteFondoService $service
     * @param ComprobanteFondoTransformer $transformer
     */
    public function __construct(Manager $fractal, ComprobanteFondoService $service, ComprobanteFondoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_comprobante_fondo')->only(['show','paginate','index','find']);
        $this->middleware('permiso:registrar_comprobante_fondo')->only(['store']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

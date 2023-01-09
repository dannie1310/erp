<?php


namespace App\Http\Controllers\v1\SEGUIMIENTO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGUIMIENTO\EmpresaTransformer;
use App\Services\SEGUIMIENTO\Finanzas\EmpresaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class EmpresaController extends Controller
{
    use ControllerTrait;

    /**
     * @var EmpresaService
     */
    protected $service;

    /**
     * @var EmpresaTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * EmpresaController constructor.
     * @param EmpresaService $service
     * @param EmpresaTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(EmpresaService $service, EmpresaTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\EmpresaTransformer;
use App\Services\CONTROLRECURSOS\EmpresaService;
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

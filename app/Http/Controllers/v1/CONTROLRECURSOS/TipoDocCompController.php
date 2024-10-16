<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\TipoDocCompTransformer;
use App\Services\CONTROLRECURSOS\TipoDocCompService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TipoDocCompController extends Controller
{
    use ControllerTrait;

    /**
     * @var TipoDocCompTransformer
     */
    protected $transformer;

    /**
     * @var TipoDocCompService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param TipoDocCompTransformer $transformer
     * @param TipoDocCompService $service
     * @param Manager $fractal
     */
    public function __construct(TipoDocCompTransformer $transformer, TipoDocCompService $service, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->transformer = $transformer;
        $this->service = $service;
        $this->fractal = $fractal;
    }


}

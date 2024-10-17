<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\CcDoctoTransformer;
use App\Services\CONTROLRECURSOS\CcDoctoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CcDoctoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var CcDoctoService
     */
    protected $service;

    /**
     * @var CcDoctoTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param CcDoctoService $service
     * @param CcDoctoTransformer $transformer
     */
    public function __construct(Manager $fractal, CcDoctoService $service, CcDoctoTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

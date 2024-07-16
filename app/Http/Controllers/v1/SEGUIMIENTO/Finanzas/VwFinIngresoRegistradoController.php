<?php

namespace App\Http\Controllers\v1\SEGUIMIENTO\Finanzas;

use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGUIMIENTO\VwFinIngresoRegistradoTransformer;
use App\Services\SEGUIMIENTO\Finanzas\VwFinIngresoRegistradoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class VwFinIngresoRegistradoController extends Controller
{
    use ControllerTrait;

    /**
     * @var VwFinIngresoRegistradoService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var VwFinIngresoRegistradoTransformer
     */
    protected $transformer;

    /**
     * @param VwFinIngresoRegistradoService $service
     * @param Manager $fractal
     * @param VwFinIngresoRegistradoTransformer $transformer
     */
    public function __construct(VwFinIngresoRegistradoService $service, Manager $fractal, VwFinIngresoRegistradoTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}

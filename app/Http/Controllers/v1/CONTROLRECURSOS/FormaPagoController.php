<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\FormaPagoTransformer;
use App\Services\CONTROLRECURSOS\FormaPagoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class FormaPagoController extends Controller
{
    use ControllerTrait;

    /**
     * @var FormaPagoService
     */
    protected $service;

    /**
     * @var FormaPagoTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param FormaPagoService $service
     * @param FormaPagoTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(FormaPagoService $service, FormaPagoTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

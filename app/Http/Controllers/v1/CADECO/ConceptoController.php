<?php

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\ConceptoTransformer;
use App\Services\CADECO\ConceptoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ConceptoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ConceptoService
     */
    protected $service;

    /**
     * @var ConceptoTransformer
     */
    protected $transformer;

    /**
     * ConceptoController constructor.
     * @param Manager $fractal
     * @param ConceptoService $service
     * @param ConceptoTransformer $transformer
     */
    public function __construct(Manager $fractal, ConceptoService $service, ConceptoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function conceptosHijosMedible($id)
    {
        return $this->service->conceptosMedible($id);
    }
}

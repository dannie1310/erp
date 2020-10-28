<?php

namespace App\Http\Controllers\v1\CADECO\Presupuesto;


use App\Http\Controllers\Controller;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

use App\Services\CADECO\Presupuesto\ConceptoService as Service;
use App\Http\Transformers\CADECO\Presupuesto\ConceptoTransformer as Transformer;

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
     * @param Service $service
     * @param Transformer $transformer
     */
    public function __construct(Manager $fractal, Service $service, Transformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function list($nivel_padre=''){
        $conceptos= $this->service->list($nivel_padre);
        return $this->respondWithCollection($conceptos);
    }
}

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

    public function list($id_padre=''){
        $conceptos= $this->service->list($id_padre);
        return $this->respondWithCollection($conceptos);
    }

    public function actualizarClaves(Request $request)
    {
        $items = $this->service->actualizarClaves($request->all());
        return $this->respondWithCollection($items);
    }
    public function actualizarClave(Request $request)
    {
        $item = $this->service->actualizarClave($request->all());
        return $this->respondWithItem($item);
    }
    //
    public function actualizaDatosSeguimiento($id,Request $request)
    {
        $item = $this->service->actualizaDatosSeguimiento($id,$request->all());
        $this->fractal->parseIncludes("dato");
        return $this->respondWithItem($item);
    }
    public function toggleActivo($id)
    {
        $item = $this->service->toggleActivo($id);
        return $this->respondWithCollection($item);
    }
    public function eliminaResponsable($id)
    {
        $item = $this->service->eliminaResponsable($id);
        return $this->respondWithItem($item);
    }
    public function storeResponsable(Request $request){
        $item = $this->service->storeResponsable($request->all());
        $this->fractal->parseIncludes("responsables.usuario");
        return $this->respondWithItem($item);

    }
}

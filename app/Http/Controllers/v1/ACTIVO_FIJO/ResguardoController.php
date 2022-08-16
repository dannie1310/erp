<?php

namespace App\Http\Controllers\v1\ACTIVO_FIJO;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\ACTIVO_FIJO\ResguardoService;
use App\Http\Transformers\ACTIVO_FIJO\ResguardoTransformer;

class ResguardoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ResguardoService
     */
    protected $service;

    /**
     * @var ResguardoTransformer
     */
    protected $transformer;

    /**
     * ResguardoController constructor.
     *
     * @param Manager $fractal
     * @param ResguardoService $service
     * @param ResguardoTransformer $transformer
     */
    public function __construct(Manager $fractal, ResguardoService $service, ResguardoTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function listaResguardos(Request $request){
        return $this->service->listaResguardos($request);
    }

    public function getResguardos(Request $request){
        $resg = $this->service->getResguardos($request->all());
        return $this->respondWithPaginator($resg);
    }

    public function pdfResguardo($id){
        return $this->service->pdfResguardo($id);
    }
}

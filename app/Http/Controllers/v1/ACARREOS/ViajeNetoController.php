<?php


namespace App\Http\Controllers\v1\ACARREOS;


use App\Http\Controllers\Controller;
use App\Http\Transformers\ACARREOS\ViajeNetoTransformer;
use App\Services\ACARREOS\ViajeNetoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class ViajeNetoController extends Controller
{
    use ControllerTrait;
    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ViajeNetoService
     */
    protected  $service;

    /**
     * @var ViajeNetoTransformer
     */
    protected $transformer;

    /**
     * ViajeNetoController constructor.
     * @param Manager $fractal
     * @param ViajeNetoService $service
     * @param ViajeNetoTransformer $transformer
     */
    public function __construct(Manager $fractal, ViajeNetoService $service, ViajeNetoTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function catalogo(Request $request)
    {
        return $this->service->getCatalogo($request->all());
    }

    public function store(Request $request)
    {dd("s");
        $item = $this->service->store($request->all());
        return $this->respondWithItem($item);
    }
}

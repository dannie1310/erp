<?php


namespace App\Http\Controllers\v1\CADECO\Almacenes;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Almacenes\CtgTipoConteoTransformer;
use App\Services\CADECO\Almacenes\CtgTipoConteoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CtgTipoConteoController extends Controller
{
    use ControllerTrait;

    /**
     * @var CtgTipoConteoService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var CtgTipoConteoTransformer
     */
    protected $transformer;

    /**
     * CtgTipoConteoController constructor.
     * @param CtgTipoConteoService $service
     * @param Manager $fractal
     * @param CtgTipoConteoTransformer $transformer
     */
    public function __construct(CtgTipoConteoService $service, Manager $fractal, CtgTipoConteoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_conteos')->only(['paginate','index','show']);

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

}
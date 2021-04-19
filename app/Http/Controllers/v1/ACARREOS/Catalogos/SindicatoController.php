<?php


namespace App\Http\Controllers\v1\ACARREOS\Catalogos;


use App\Http\Controllers\Controller;
use App\Http\Transformers\ACARREOS\Catalogos\SindicatoTransformer;
use App\Services\ACARREOS\Catalogos\SindicatoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SindicatoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SindicatoService
     */
    protected  $service;

    /**
     * @var SindicatoTransformer
     */
    protected $transformer;

    /**
     * SindicatoController constructor.
     * @param Manager $fractal
     * @param SindicatoService $service
     * @param SindicatoTransformer $transformer
     */
    public function __construct(Manager $fractal, SindicatoService $service, SindicatoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

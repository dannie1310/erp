<?php

namespace App\Http\Controllers\v1\CADECO\Tesoreria;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Tesoreria\TraspasoCuentasTransformer;
use App\Services\CADECO\Tesoreria\TraspasoEntreCuentasService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TraspasoEntreCuentasController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TraspasoEntreCuentasService
     */
    protected $service;

    /**
     * @var TraspasoCuentasTransformer
     */
    protected $transformer;

    /**
     * TraspasoEntreCuentasController constructor.
     * @param Manager $fractal
     * @param TraspasoEntreCuentasService $service
     * @param TraspasoCuentasTransformer $transformer
     */
    public function __construct(Manager $fractal, TraspasoEntreCuentasService $service, TraspasoCuentasTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
<?php

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\CostoTransformer;
use App\Services\CADECO\CostoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CostoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var CostoService
     */
    protected $service;

    /**
     * @var CostoTransformer
     */
    protected $transformer;

    /**
     * CostoController constructor.
     * @param Manager $fractal
     * @param CostoService $service
     * @param CostoTransformer $transformer
     */
    public function __construct(Manager $fractal, CostoService $service, CostoTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
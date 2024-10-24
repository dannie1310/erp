<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\SerieTransformer;
use App\Services\CONTROLRECURSOS\SerieService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SerieController extends Controller
{
    use ControllerTrait;

    /**
     * @var SerieService
     */
    protected $service;

    /**
     * @var SerieTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param SerieService $service
     * @param SerieTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(SerieService $service, SerieTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

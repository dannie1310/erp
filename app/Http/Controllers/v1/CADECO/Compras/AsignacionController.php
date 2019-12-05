<?php


namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Compras\AsignacionTransformer;
use App\Services\CADECO\Compras\AsignacionService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class AsignacionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var AsignacionService
     */

    private $service;

    /**
     * @var AsignacionTransformer
     */
    private $transformer;

    /**
     * AsignacionController constructor.
     * @param Manager $fractal
     * @param AsignacionService $service
     * @param AsignacionTransformer $transformer
     */

    public function __construct(Manager $fractal, AsignacionService $service, AsignacionTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

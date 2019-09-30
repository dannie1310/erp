<?php


namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\FamiliaTransformer;
use App\Services\CADECO\FamiliaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class FamiliaController extends Controller
{
    use ControllerTrait;

    /**
     * @var FamiliaService
     */
    protected $service;

    /**
     * @var FamiliaTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * FamiliaController constructor.
     * @param FamiliaService $service
     * @param FamiliaTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(FamiliaService $service, FamiliaTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }

}

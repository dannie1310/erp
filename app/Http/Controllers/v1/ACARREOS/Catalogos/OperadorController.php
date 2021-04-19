<?php


namespace App\Http\Controllers\v1\ACARREOS\Catalogos;


use App\Http\Controllers\Controller;
use App\Http\Transformers\ACARREOS\Catalogos\OperadorTransformer;
use App\Services\ACARREOS\Catalogos\OperadorService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class OperadorController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var OperadorService
     */
    protected  $service;

    /**
     * @var OperadorTransformer
     */
    protected $transformer;

    /**
     * OperadorController constructor.
     * @param Manager $fractal
     * @param OperadorService $service
     * @param OperadorTransformer $transformer
     */
    public function __construct(Manager $fractal, OperadorService $service, OperadorTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

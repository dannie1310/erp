<?php


namespace App\Http\Controllers\v1\ACARREOS\Catalogos;


use App\Http\Controllers\Controller;
use App\Http\Transformers\ACARREOS\Catalogos\TipoOrigenTransformer;
use App\Services\ACARREOS\Catalogos\TipoOrigenService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TipoOrigenController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TipoOrigenService
     */
    protected $service;

    /**
     * @var TipoOrigenTransformer
     */
    protected $transformer;

    /**
     * TipoOrigenController constructor.
     * @param Manager $fractal
     * @param TipoOrigenService $service
     * @param TipoOrigenTransformer $transformer
     */
    public function __construct(Manager $fractal, TipoOrigenService $service, TipoOrigenTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

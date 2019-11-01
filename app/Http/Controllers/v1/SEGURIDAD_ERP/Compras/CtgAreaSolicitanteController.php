<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Compras\CtgAreaSolicitanteTransformer;
use App\Services\SEGURIDAD_ERP\Compras\CtgAreaSolicitanteService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CtgAreaSolicitanteController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;


    /**
     * @var CtgAreaSolicitanteService
     */
    protected $service;


    /**
     * @var CtgAreaSolicitanteTransformer
     */
    protected $transformer;


    /**
     * CtgAreaSolicitanteController constructor
     * @param Manager $fractal
     * @param CtgAreaSolicitanteService $service
     * @param CtgAreaSolicitanteTransformer $transformer
     */


    public function __construct(Manager $fractal, CtgAreaSolicitanteService $service, CtgAreaSolicitanteTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Compras\CtgAreaCompradoraTransformer;
use App\Services\SEGURIDAD_ERP\Compras\CtgAreaCompradoraService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CtgAreaCompradoraController extends  Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;


    /**
     *@var CtgAreaCompradoraService
     */
    protected $service;


    /**
     * @var CtgAreaCompradoraTransformer
     */
    protected $transformer;

    /**
     * CtgAreaCompradoraController constructor
     * @parama Manager $fractal
     * @param CtgAreaCompradoraService $service
     * @param CtgAreaCompradoraTransformer $transformer
     */


    public function __construct(Manager $fractal, CtgAreaCompradoraService $service, CtgAreaCompradoraTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}

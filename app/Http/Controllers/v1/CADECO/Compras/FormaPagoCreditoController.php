<?php


namespace App\Http\Controllers\v1\CADECO\Compras;


use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\CADECO\Compras\ItemContratistaService;
use App\Services\CADECO\Compras\FormaPagoCreditoService;
use App\Http\Transformers\CADECO\Compras\ItemContratistaTransformer;
use App\Http\Transformers\CADECO\Compras\FormaPagoCreditoTransformer;

class FormaPagoCreditoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var FormaPagoCreditoService
     */
    protected $service;

    /**
     * @var FormaPagoCreditoTransformer
     */
    protected $transformer;

    /**
     * MaterialController constructor.
     * @param Manager $fractal
     * @param ItemContratistaService $service
     * @param ItemContratistaTransformer $transformer
     */
    public function __construct(Manager $fractal,FormaPagoCreditoService $service, FormaPagoCreditoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }


}
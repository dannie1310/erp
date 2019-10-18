<?php


namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Compras\ItemContratistaTransformer;
use App\Services\CADECO\Compras\ItemContratistaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ItemContratistaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ItemContratistaService
     */
    protected $service;

    /**
     * @var ItemContratistaTransformer
     */
    protected $transformer;

    /**
     * MaterialController constructor.
     * @param Manager $fractal
     * @param ItemContratistaService $service
     * @param ItemContratistaTransformer $transformer
     */
    public function __construct(Manager $fractal,ItemContratistaService $service, ItemContratistaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }


}
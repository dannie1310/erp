<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/05/2019
 * Time: 02:13 PM
 */

namespace App\Http\Controllers\v1\CADECO\Almacenes;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Almacenes\OrdenCompraTransformer;
use App\Services\CADECO\Compras\OrdenCompraService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class OrdenCompraController extends Controller
{
    use ControllerTrait;

    /**
     * @var OrdenCompraService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var OrdenCompraTransformer
     */
    private $transformer;

    /**
     * OrdenCompraController constructor.
     * @param OrdenCompraService $service
     * @param Manager $fractal
     * @param OrdenCompraTransformer $transformer
     */
    public function __construct(OrdenCompraService $service, Manager $fractal, OrdenCompraTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_orden_compra')->only('paginate');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
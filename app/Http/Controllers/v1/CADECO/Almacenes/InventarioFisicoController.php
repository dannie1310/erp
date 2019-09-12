<?php


namespace App\Http\Controllers\v1\CADECO\Almacenes;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Almacenes\InventarioFisicoTransformer;
use App\Services\CADECO\Almacenes\InventarioFisicoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class InventarioFisicoController extends Controller
{
    use ControllerTrait;

    /**
     * @var InventarioFisicoService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var InventarioFisicoTransformer
     */
    protected $transformer;

    /**
     * InventarioFisicoController constructor.
     * @param InventarioFisicoService $service
     * @param Manager $fractal
     * @param InventarioFisicoTransformer $transformer
     */
    public function __construct(InventarioFisicoService $service, Manager $fractal, InventarioFisicoTransformer $transformer)
    {
        $this->middleware('addAccessToken')->only('pdf_marbetes');
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_inventario_fisico')->only('paginate');
        $this->middleware('permiso:iniciar_conteo_inventario_fisico')->only('store');


        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function pdf_marbetes($id){
        return $this->service->generar_marbetes($id);
    }

}

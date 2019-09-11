<?php


namespace App\Http\Controllers\v1\CADECO\Almacenes;


use App\Http\Controllers\Controller;
use App\PDF\InventarioMarbete;
use App\Traits\ControllerTrait;

class AlmacenController extends Controller
{
    use ControllerTrait;

    protected $fractal;


    protected $service;


    protected $transformer;

    /**
     * SolicitudCompraController constructor.
     * @param Manager $fractal
     * @param SolicitudCompraService $service
     * @param SolicitudCompraTransformer $transformer
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('context');

//        $this->fractal = $fractal;
//        $this->service = $service;
//        $this->transformer = $transformer;
    }

    public function marbetes(){
        $marbete = new InventarioMarbete();
        $marbete->create();
    }

}

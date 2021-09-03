<?php

namespace App\Http\Controllers\v1\CADECO\ControlPresupuesto;

use App\Http\Transformers\CADECO\ControlPresupuesto\ExtraordinarioTransformer;
use App\Services\CADECO\ControlPresupuesto\ExtraordinarioService;
use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;

class ExtraordinarioController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ExtraordinarioService
     */
    protected $service;


    /**
     * @var ExtraordinarioTransformer
     */
    protected $transformer;


    /**
     * @param Manager $fractal
     * @param ExtraordinarioService $service
     * @param ExtraordinarioTransformer $transformer
     */

    public function __construct(Manager $fractal, ExtraordinarioService $service, ExtraordinarioTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_extraordinario')->only(['paginate', 'pdfVariacionVolumen', 'show']);
        $this->middleware('permiso:registrar_extraordinario')->only(['store']);
        $this->middleware('permiso:autorizar_extraordinario')->only(['autorizar']);
        $this->middleware('permiso:rechazar_extraordinario')->only(['destroy']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function autorizar($id){
         $resp =  $this->service->autorizar($id);
         return $this->respondWithItem($resp);
    }

    public function pdfVariacionVolumen($id)
    {
        if(auth()->user()->can('consultar_entrada_almacen') || true) {
            return $this->service->pdfVariacionVolumen($id)->create();
        }
        dd( 'No cuentas con los permisos necesarios para realizar la acción solicitada');
    }

}

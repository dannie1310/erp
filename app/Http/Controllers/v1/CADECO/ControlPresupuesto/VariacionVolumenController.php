<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 0:58 PM
 */

namespace App\Http\Controllers\v1\CADECO\ControlPresupuesto;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\CADECO\ControlPresupuesto\VariacionVolumenService;
use App\Http\Transformers\CADECO\ControlPresupuesto\VariacionVolumenTransformer;

class VariacionVolumenController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var VariacionVolumenService
     */
    protected $service;


    /**
     * @var VariacionVolumenTransformer
     */
    protected $transformer;


    /**
     * SolicitudCambioController constructor
     *
     * @param Manager $fractal
     * @param VariacionVolumenService $service
     * @param VariacionVolumenTransformer $transformer
     */

    public function __construct(Manager $fractal, VariacionVolumenService $service, VariacionVolumenTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_variacion_volumen')->only(['paginate', 'pdfVariacionVolumen', 'show']);
        $this->middleware('permiso:registrar_variacion_volumen')->only(['store']);
        $this->middleware('permiso:autorizar_variacion_volumen')->only(['autorizar']);
        $this->middleware('permiso:rechazar_variacion_volumen')->only(['destroy']);

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
        if(auth()->user()->can('consultar_variacion_volumen')) {
            return $this->service->pdfVariacionVolumen($id)->create();
        }
        dd( 'No cuentas con los permisos necesarios para consultar el formato de variación de volumen.');
    }

}

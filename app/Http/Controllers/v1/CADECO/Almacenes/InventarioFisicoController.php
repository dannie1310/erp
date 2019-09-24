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
        $this->middleware('permiso:iniciar_inventario_fisico')->only('store');
//        $this->middleware('permiso:generar_marbetes')->only('pdf_marbetes');
        $this->middleware('permiso:descarga_layout_captura_conteos')->only('descargaLayout');
        $this->middleware('permiso:descargar_resumen_conteos')->only('descargar_resumen_conteo');
        $this->middleware('permiso:cerrar_inventario_fisico')->only('actualizar');


        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function pdf_marbetes($id){
        if(auth()->user()->can('generar_marbetes')){
            return $this->service->generar_marbetes($id);
        }
        dd( 'No cuentas con los permisos necesarios para realizar la acción solicitada');
    }

    public function descargaLayout($id)
    {
        if(auth()->user()->can('descarga_layout_captura_conteos')){
            return $this->service->descargaLayout($id);
        }
        dd( 'No cuentas con los permisos necesarios para realizar la acción solicitada');

    }

    public function actualizar($id)
    {
        $item = $this->service->actualizar($id);
        return $this->respondWithItem($item);
    }


    public function descargar_resumen_conteo($id){
        return $this->service->generar_resumen_conteos($id);
    }

}

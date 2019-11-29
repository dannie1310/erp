<?php


namespace App\Http\Controllers\v1\CADECO\Almacenes;


use App\Http\Controllers\Controller;
use App\Http\Requests\Almacenes\DeleteSalidaAlmacenRequest;
use App\Http\Transformers\CADECO\Compras\SalidaAlmacenTransformer;
use App\Services\CADECO\Almacenes\SalidaAlmacenService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SalidaAlmacenController extends Controller
{
    use ControllerTrait {
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var SalidaAlmacenService
     */
    private $service;

    /**
     * @var SalidaAlmacenTransformer
     */
    private $transformer;

    /**
     * SalidaAlmacenController constructor.
     * @param Manager $fractal
     * @param SalidaAlmacenService $service
     * @param SalidaAlmacenTransformer $transformer
     */

    public function __construct(Manager $fractal, SalidaAlmacenService $service, SalidaAlmacenTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_salida_almacen')->only(['show','paginate','index','find']);
        $this->middleware('permiso:eliminar_salida_almacen')->only(['destroy']);
        $this->middleware('permiso:registrar_salida_almacen')->only(['store']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function destroy(DeleteSalidaAlmacenRequest $request, $id)
    {
        return $this->traitDestroy($request, $id);
    }

    public function pdfSalidaAlmacen($id)
    {
        if(auth()->user()->can('consultar_salida_almacen')) {
            return $this->service->pdfSalidaAlmacen($id)->create();
        }
        dd( 'No cuentas con los permisos necesarios para realizar la acción solicitada');

    }

}

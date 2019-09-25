<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 12:55 PM
 */

namespace App\Http\Controllers\v1\CADECO\Almacenes;


use App\Http\Controllers\Controller;
use App\Http\Requests\Compras\DeleteEntradaAlmacenRequest;
use App\Http\Transformers\CADECO\Compras\EntradaAlmacenTransformer;
use App\Services\CADECO\Almacenes\EntradaAlmacenService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class EntradaAlmacenController extends Controller
{
    use ControllerTrait {
        destroy as traitDestroy;
    }

    /**
     * @var EntradaAlmacenService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var EntradaAlmacenTransformer
     */
    private $transformer;

    /**
     * EntradaAlmacenController constructor.
     * @param EntradaAlmacenService $service
     * @param Manager $fractal
     * @param EntradaAlmacenTransformer $transformer
     */
    public function __construct(EntradaAlmacenService $service, Manager $fractal, EntradaAlmacenTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_entrada_almacen')->only(['show','paginate','index','find']);
        $this->middleware('permiso:eliminar_entrada_almacen')->only('destroy');
      

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function destroy(DeleteEntradaAlmacenRequest $request, $id)
    {
        return $this->traitDestroy($request, $id);
    }

    public function pdfEntradaAlmacen($id)
    {
        if(auth()->user()->can('consultar_entrada_almacen')) {
            return $this->service->pdfEntradaAlmacen($id)->create();
        }
        dd( 'No cuentas con los permisos necesarios para realizar la acción solicitada');
    }
}

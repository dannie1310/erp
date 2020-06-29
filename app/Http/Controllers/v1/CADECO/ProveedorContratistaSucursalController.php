<?php
/**
 * Created by PhpStorm.
 * User: JLOpeza
 * Date: 23/06/2020
 * Time: 2:47 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\CADECO\ProveedorContratistaSucursalService;
use App\Http\Transformers\CADECO\ProveedorContratistaSucursalTransformer;

class ProveedorContratistaSucursalController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ProveedorContratistaSucursalService
     */
    protected $service;

    /**
     * @var ProveedorContratistaSucursalTransformer
     */
    protected $transformer;

    /**
     * SucursalController constructor
     *
     * @param Manager $fractal
     * @param SucursalService $service
     * @param SucursalTransformer $transformer
     */

    public function __construct(Manager $fractal, ProveedorContratistaSucursalService $service, ProveedorContratistaSucursalTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        // $this->middleware('permiso:registrar_sucursal_banco')->only(['store']);
        $this->middleware('permiso:consultar_sucursal_proveedor')->only(['paginate','index','show']);
        // $this->middleware('permiso:editar_sucursal_banco')->only(['update']);
        $this->middleware('permiso:eliminar_sucursal_proveedor')->only(['destroy']);
        $this->middleware('permiso:editar_sucursal_proveedor')->only(['updateProveedorSucursal']);
        $this->middleware('permiso:registrar_sucursal_proveedor')->only(['storeProveedorSucursal']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;

    }

    public function storeProveedorSucursal(Request $request){
        $sucursal = $this->service->store($request->all());
        return $this->respondWithItem($sucursal);
    }

    public function updateProveedorSucursal(Request $request, $id){
        $sucursal = $this->service->update($request->all(), $id);
        return $this->respondWithItem($sucursal);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 08/08/2019
 * Time: 7:47 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\SucursalTransformer;
use App\Services\CADECO\SucursalService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SucursalService
     */
    protected $service;

    /**
     * @var SucursalTransformer
     */
    protected $transformer;

    /**
     * SucursalController constructor
     *
     * @param Manager $fractal
     * @param SucursalService $service
     * @param SucursalTransformer $transformer
     */

    public function __construct(Manager $fractal, SucursalService $service, SucursalTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:registrar_sucursal_banco')->only(['store']);
        $this->middleware('permiso:consultar_sucursal_banco')->only(['paginate','index','show']);
        $this->middleware('permiso:editar_sucursal_banco')->only(['update']);
        // $this->middleware('permiso:eliminar_sucursal_proveedor')->only(['destroy']);
        // $this->middleware('permiso:editar_sucursal_proveedor')->only(['updateProveedorSucursal']);
        // $this->middleware('permiso:registrar_sucursal_proveedor')->only(['storeProveedorSucursal']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;

    }

    // public function storeProveedorSucursal(Request $request){
    //     $sucursal = $this->service->store($request->all());
    //     return $this->respondWithItem($sucursal);
    // }

    // public function updateProveedorSucursal(Request $request, $id){
    //     $sucursal = $this->service->update($request->all(), $id);
    //     return $this->respondWithItem($sucursal);
    // }
}

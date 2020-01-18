<?php
/**
 * Created by PhpStorm.
 * User: JlopezA
 * Date: 03/01/2020
 * Time: 01:26 PM
 */

namespace App\Http\Controllers\v1\CADECO;

use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\CADECO\ProveedorContratistaService;
use App\Http\Transformers\CADECO\ProveedorContratistaTransformer;

class ProveedorContratistaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ProveedorContratistaService
     */
    protected $service;

    /**
     * @var ProveedorContratistaTransformer
     */
    protected $transformer;

    /**
     * MaterialController constructor.
     * @param Manager $fractal
     * @param ProveedorContratistaService $service
     * @param ProveedorContratistaTransformer $transformer
     */
    public function __construct(Manager $fractal, ProveedorContratistaService $service, ProveedorContratistaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        // $this->middleware('permiso:consultar_proveedor')->only(['show','paginate','index','find']);
        $this->middleware('permiso:eliminar_proveedor')->only(['destroy']);
        $this->middleware('permiso:editar_proveedor')->only(['update']);
        $this->middleware('permiso:registrar_proveedor')->only(['store']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
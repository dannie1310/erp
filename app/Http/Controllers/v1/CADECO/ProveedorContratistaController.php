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
        // $this->middleware('permiso:consultar_insumo_material|consultar_insumo_herramienta_equipo|consultar_insumo_servicio|consultar_insumo_maquinaria')->only(['show','paginate','index','find']);
        // $this->middleware('permiso:registrar_insumo_material|registrar_insumo_herramienta_equipo|registrar_insumo_servicio|registrar_insumo_maquinaria')->only('store');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
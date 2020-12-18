<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 07/02/19
 * Time: 04:51 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\AlmacenTransformer;
use App\Services\CADECO\AlmacenService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class AlmacenController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var AlmacenService
     */
    protected $service;

    /**
     * @var AlmacenTransformer
     */
    protected $transformer;

    /**
     * AlmacenController constructor.
     *
     * @param Manager $fractal
     * @param AlmacenService $service
     * @param AlmacenTransformer $transformer
     */
    public function __construct(Manager $fractal, AlmacenService $service, AlmacenTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:registrar_almacen_material|registrar_almacen_maquinaria|registrar_almacen_maquina_controladora_insumo|registrar_almacen_mano_obra|registrar_almacen_servicio|registrar_almacen_herramienta')->only('store');
        $this->middleware('permiso:editar_almacen_material|editar_almacen_maquinaria|editar_almacen_maquina_controladora_insumo|editar_almacen_mano_obra|editar_almacen_servicio|editar_almacen_herramienta')->only('update');
        $this->middleware('permiso:consultar_almacen_material|consultar_almacen_maquinaria|consultar_almacen_maquina_controladora_insumo|consultar_almacen_mano_obra|consultar_almacen_servicio|consultar_almacen_herramienta')->only(['show','paginate','index','find']);
        $this->middleware('permiso:eliminar_almacen_material|eliminar_almacen_maquinaria|eliminar_almacen_maquina_controladora_insumo|eliminar_almacen_mano_obra|eliminar_almacen_servicio|eliminar_almacen_herramienta')->only('destroy');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

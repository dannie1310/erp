<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 14/02/19
 * Time: 09:06 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteMaterialRequest;
use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Services\CADECO\MaterialService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class MaterialController extends Controller
{
    use ControllerTrait{
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var MaterialService
     */
    protected $service;

    /**
     * @var MaterialTransformer
     */
    protected $transformer;

    /**
     * MaterialController constructor.
     * @param Manager $fractal
     * @param MaterialService $service
     * @param MaterialTransformer $transformer
     */
    public function __construct(Manager $fractal, MaterialService $service, MaterialTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_insumo_mano_obra|consultar_insumo_material|consultar_insumo_herramienta_equipo|consultar_insumo_servicio|consultar_insumo_maquinaria')->only(['show','paginate','index','find']);
        $this->middleware('permiso:registrar_insumo_mano_obra|registrar_insumo_material|registrar_insumo_herramienta_equipo|registrar_insumo_servicio|registrar_insumo_maquinaria')->only('store');
        $this->middleware('permiso:editar_insumo_servicio|editar_insumo_material|editar_insumo_herramienta_equipo|editar_insumo_maquinaria|editar_insumo_mano_obra')->only('update');
        $this->middleware('permiso:eliminar_insumo_servicio|eliminar_insumo_material|eliminar_insumo_maquinaria|eliminar_insumo_mano_obra')->only('destroy');



        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function descargar_lista_material(Request $request)
    {
        return $this->service->catalogo_insumos($request->scope);
    }

    public function destroy(DeleteMaterialRequest $request, $id)
    {
        return $this->traitDestroy($request, $id);
    }

    public function buscarMateriales(Request $request){
        $materiales = $this->service->buscarMateriales($request->all());
        return $this->respondWithCollection($materiales);
    }

    public function materialPorAlmacen(Request $request, $id)
    {
        return $this->materialPorAlmacen($id);
    }
}


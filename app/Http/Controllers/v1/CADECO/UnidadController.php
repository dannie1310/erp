<?php


namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\UnidadTransformer;
use App\Services\CADECO\UnidadService;
use App\Traits\ControllerTrait;
use App\Http\Requests\EliminarUnidadRequest;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class UnidadController extends Controller
{
    use ControllerTrait{
        destroy as traitDestroy;
    }

    /**
     * @var UnidadService
     */
    protected $service;

    /**
     * @var UnidadTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * UnidadController constructor.
     * @param UnidadService $service
     * @param UnidadTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(UnidadService $service, UnidadTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');
        $this->middleware('context')->except(['unidadesGlobal']);
        $this->middleware('permiso:registrar_unidad')->only(['store']);
        $this->middleware('permiso:consultar_unidad')->only(['paginate']);
        $this->middleware('permiso:eliminar_unidad')->only(['destroy']);
        $this->middleware('permiso:editar_unidad')->only(['update']);

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }

    public function destroy(EliminarUnidadRequest $request, $id)
    {
        return $this->traitDestroy($request, $id);
    }

    public function unidadesGlobal(Request $request)
    {
        return $this->respondWithCollection($this->service->globales($request->all()));
    }
}

<?php


namespace App\Http\Controllers\v1\CADECO\Almacenes;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Almacenes\MarbeteTransformer;
use App\Services\CADECO\Almacenes\MarbeteService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class MarbeteController extends Controller
{
    use ControllerTrait;

    /**
     * @var MarbeteService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var MarbeteTransformer
     */
    protected $transformer;

    /**
     * MarbeteController constructor.
     * @param MarbeteService $service
     * @param Manager $fractal
     * @param MarbeteTransformer $transformer
     */

    public function __construct(MarbeteService $service, Manager $fractal, MarbeteTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_marbetes')->only(['paginate','index','show']);
        $this->middleware('permiso:registrar_marbetes_manualmente')->only(['store']);
        $this->middleware('permiso:eliminar_marbetes_manualmente')->only(['delete']);

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function showCodigo(Request $request, $id)
    {
        $item = $this->service->showCodigo($id);
        return $this->respondWithItem($item);
    }
}


<?php


namespace App\Http\Controllers\v1\ACARREOS\Catalogos;


use App\Http\Controllers\Controller;
use App\Http\Transformers\ACARREOS\Catalogos\OrigenTransformer;
use App\Services\ACARREOS\Catalogos\OrigenService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class OrigenController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var OrigenService
     */
    protected $service;

    /**
     * @var OrigenTransformer
     */
    protected $transformer;

    /**
     * OrigenController constructor.
     * @param Manager $fractal
     * @param OrigenService $service
     * @param OrigenTransformer $transformer
     */
    public function __construct(Manager $fractal, OrigenService $service, OrigenTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_origen')->only(['show','paginate','index','find']);
        $this->middleware('permiso:registrar_origen')->only(['store']);
        $this->middleware('permiso:editar_origen')->only(['activar', 'desactivar', 'update']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function activar(Request $request, $id)
    {
        return $this->respondWithItem($this->service->activar($id));
    }

    public function desactivar(Request $request, $id)
    {
        return $this->respondWithItem($this->service->desactivar($request->all(),$id));
    }

    public function descargaLayout()
    {
        return $this->service->excel();
    }
}

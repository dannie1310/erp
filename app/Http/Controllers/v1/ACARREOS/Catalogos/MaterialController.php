<?php


namespace App\Http\Controllers\v1\ACARREOS\Catalogos;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\ACARREOS\Catalogos\MaterialService;
use App\Http\Transformers\ACARREOS\Catalogos\MaterialTransformer;

class MaterialController extends Controller
{
    use ControllerTrait;

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
     * OrigenController constructor.
     * @param Manager $fractal
     * @param OrigenService $service
     * @param OrigenTransformer $transformer
     */
    public function __construct(Manager $fractal, MaterialService $service, MaterialTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_material')->only(['show','paginate','index','find', 'descargaLayout']);
        $this->middleware('permiso:registrar_material')->only(['store']);
        $this->middleware('permiso:activar_desactivar_material')->only(['activar', 'desactivar']);

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

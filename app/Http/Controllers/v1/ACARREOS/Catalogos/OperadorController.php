<?php


namespace App\Http\Controllers\v1\ACARREOS\Catalogos;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\ACARREOS\Catalogos\OperadorService;
use App\Http\Transformers\ACARREOS\Catalogos\OperadorTransformer;

class OperadorController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var OperadorService
     */
    protected  $service;

    /**
     * @var OperadorTransformer
     */
    protected $transformer;

    /**
     * OperadorController constructor.
     * @param Manager $fractal
     * @param OperadorService $service
     * @param OperadorTransformer $transformer
     */
    public function __construct(Manager $fractal, OperadorService $service, OperadorTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_operador')->only(['show','paginate','index','find', 'descargaLayout']);
        $this->middleware('permiso:registrar_operador')->only(['store']);
        $this->middleware('permiso:editar_operador')->only(['update']);
        $this->middleware('permiso:activar_desactivar_operador')->only(['activar', 'desactivar']);

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

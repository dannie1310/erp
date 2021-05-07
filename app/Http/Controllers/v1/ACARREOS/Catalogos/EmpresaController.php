<?php


namespace App\Http\Controllers\v1\ACARREOS\Catalogos;


use App\Http\Controllers\Controller;
use App\Http\Transformers\ACARREOS\Catalogos\EmpresaTransformer;
use App\Services\ACARREOS\Catalogos\EmpresaService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class EmpresaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var EmpresaService
     */
    protected  $service;

    /**
     * @var EmpresaTransformer
     */
    protected $transformer;

    /**
     * EmpresaController constructor.
     * @param Manager $fractal
     * @param EmpresaService $service
     * @param EmpresaTransformer $transformer
     */
    public function __construct(Manager $fractal, EmpresaService $service, EmpresaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_empresa')->only(['show','paginate','index','find']);
        $this->middleware('permiso:editar_empresa')->only(['update']);
        $this->middleware('permiso:registrar_empresa')->only(['store']);
        $this->middleware('permiso:activar_desactivar_empresa')->only(['activar', 'desactivar']);

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

}

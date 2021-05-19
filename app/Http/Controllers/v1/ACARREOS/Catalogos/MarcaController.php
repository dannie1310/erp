<?php


namespace App\Http\Controllers\v1\ACARREOS\Catalogos;


use App\Http\Controllers\Controller;
use App\Http\Transformers\ACARREOS\Catalogos\MarcaTransformer;
use App\Services\ACARREOS\Catalogos\MarcaService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class MarcaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var MarcaService
     */
    protected  $service;

    /**
     * @var MarcaTransformer
     */
    protected $transformer;

    /**
     * MarcaController constructor.
     * @param Manager $fractal
     * @param MarcaService $service
     * @param MarcaTransformer $transformer
     */
    public function __construct(Manager $fractal, MarcaService $service, MarcaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_marca')->only(['show','paginate','index','find', 'descargaLayout']);
        $this->middleware('permiso:registrar_marca')->only(['store']);
        $this->middleware('permiso:editar_marca')->only(['update']);
        $this->middleware('permiso:activar_desactivar_marca')->only(['activar', 'desactivar']);

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

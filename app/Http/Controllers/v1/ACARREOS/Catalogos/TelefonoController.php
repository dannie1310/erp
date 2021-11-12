<?php


namespace App\Http\Controllers\v1\ACARREOS\Catalogos;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\ACARREOS\Catalogos\TelefonoService;
use App\Http\Transformers\ACARREOS\Catalogos\TelefonoTransformer;

class TelefonoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TelefonoService
     */
    protected $service;

    /**
     * @var TelefonoTransformer
     */
    protected $transformer;

    /**
     * CamionController constructor.
     * @param Manager $fractal
     * @param TelefonoService $service
     * @param TelefonoTransformer $transformer
     */
    public function __construct(Manager $fractal, TelefonoService $service, TelefonoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_telefono')->only(['show','paginate','index', 'descargaLayout']);
        $this->middleware('permiso:activar_desactivar_telefono')->only(['activar', 'desactivar']);
        $this->middleware('permiso:editar_telefono')->only(['update']);
        $this->middleware('permiso:registrar_telefono')->only(['store']);

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

    /**
     * Descargar Layout con los telefonos
     * @return mixed
     */
    public function descargaLayout()
    {
        return $this->service->excel();
    }
}

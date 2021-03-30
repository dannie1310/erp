<?php


namespace App\Http\Controllers\v1\ACARREOS\Catalogos;


use App\Http\Controllers\Controller;
use App\Http\Transformers\ACARREOS\Catalogos\CamionTransformer;
use App\Services\ACARREOS\Catalogos\CamionService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class CamionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var CamionService
     */
    protected $service;

    /**
     * @var CamionTransformer
     */
    protected $transformer;

    /**
     * CamionController constructor.
     * @param Manager $fractal
     * @param CamionService $service
     * @param CamionTransformer $transformer
     */
    public function __construct(Manager $fractal, CamionService $service, CamionTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context')->except(['catalogo','cambiarClave','registrar','cargaImagenes']);

        $this->middleware('permiso:consultar_origen')->only(['show','paginate','index','find']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    /**
     * Obtener catálogos para el uso de la aplicación móvil "Catálogo Camiones"
     * @param Request $request
     * @return array|false|string
     * @throws \Exception
     */
    public function catalogo(Request $request)
    {
        return $this->service->getCatalogo($request->all());
    }

    /**
     * Cambiar la contraseña del usuario desde la aplicación móvil "Catálogo Camiones"
     * @param Request $request
     * @return false|string
     * @throws \Exception
     */
    public function cambiarClave(Request $request)
    {
        return $this->service->cambiarClave($request->all());
    }

    /**
     * Registrar camiones desde aplicación móvil "Catálogo Camiones"
     * @param Request $request
     * @return false|string
     * @throws \Exception
     */
    public function registrar(Request $request)
    {
        return $this->service->registrar($request->all());
    }

    /**
     * Registrar imágenes desde aplicación móvil "Catálogo Camiones"
     * @param Request $request
     * @return false|string
     * @throws \Exception
     */
    public function cargaImagenes(Request $request)
    {
        return $this->service->cargaImagenes($request->all());
    }
}

<?php


namespace App\Http\Controllers\v1\ACARREOS;


use App\Http\Controllers\Controller;
use App\Http\Transformers\ACARREOS\TagTransformer;
use App\Services\ACARREOS\TagService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class TagController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TagService
     */
    protected $service;

    /**
     * @var TagTransformer
     */
    protected $transformer;

    /**
     * TagController constructor.
     * @param Manager $fractal
     * @param TagService $service
     * @param TagTransformer $transformer
     */
    public function __construct(Manager $fractal, TagService $service, TagTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    /**
     * Obtener catálogos para el uso de la aplicación 'Configuración TAGS'.
     * @param Request $request
     * @return array|false|string
     * @throws \Exception
     */
    public function catalogo(Request $request)
    {
        return $this->service->getCatalogo($request->all());
    }

    /**
     * Registrar la configuración TAGS desde aplicación móvil 'Configuración TAGS'.
     * @param Request $request
     * @return false|string
     * @throws \Exception
     */
    public function registrarTag(Request $request)
    {
        return $this->service->registrar($request->all());
    }
}

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
}

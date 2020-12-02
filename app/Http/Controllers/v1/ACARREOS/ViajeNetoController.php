<?php


namespace App\Http\Controllers\v1\ACARREOS;


use App\Http\Controllers\Controller;
use App\Http\Transformers\ACARREOS\ViajeNetoTransformer;
use App\Services\ACARREOS\ViajeNetoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class ViajeNetoController extends Controller
{
    use ControllerTrait;
    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ViajeNetoService
     */
    protected  $service;

    /**
     * @var ViajeNetoTransformer
     */
    protected $transformer;

    /**
     * ViajeNetoController constructor.
     * @param Manager $fractal
     * @param ViajeNetoService $service
     * @param ViajeNetoTransformer $transformer
     */
    public function __construct(Manager $fractal, ViajeNetoService $service, ViajeNetoTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    /**
     * Obtener catálogos para el uso de la aplicación móvil
     * @param Request $request
     * @return array|false|string
     * @throws \Exception
     */
    public function catalogo(Request $request)
    {
        return $this->service->getCatalogo($request->all());
    }

    /**
     * Viajes a registrar desde aplicación móvil
     * @param Request $request
     * @return false|string
     * @throws \Exception
     */
    public function registrarViaje(Request $request)
    {
        return $this->service->registrarViaje($request->all());
    }

    /**
     * Registrar imágenes desde aplicación móvil
     * @param Request $request
     * @return false|string
     * @throws \Exception
     */
    public function cargaImagenesViajes(Request $request){
        return $this->service->cargaImagenesViajes($request->all());
    }

    /**
     * Cambiar la contraseña del usuario desde la aplicación móvil de acarreos
     * @param Request $request
     * @return false|string
     * @throws \Exception
     */
    public function cambiarClave(Request $request)
    {
        return $this->service->cambiarClave($request->all());
    }
}

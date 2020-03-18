<?php


namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\ConfiguracionEstimacionTransformer;
use App\Services\CADECO\Finanzas\ConfiguracionEstimacionService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ConfiguracionEstimacionController extends Controller
{
    use ControllerTrait;

    /**
     * @var ConfiguracionEstimacionService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var ConfiguracionEstimacionTransformer
     */
    private $transformer;

    /**
     * ConfiguracionEstimacionController constructor.
     * @param ConfiguracionEstimacionService $service
     * @param Manager $fractal
     * @param ConfiguracionEstimacionTransformer $transformer
     */
    public function __construct(ConfiguracionEstimacionService $service, Manager $fractal, ConfiguracionEstimacionTransformer $transformer){
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:editar_configuracion_finanzas_estimaciones')->except('index');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

}
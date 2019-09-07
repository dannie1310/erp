<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\ConfiguracionRemesaTransformer;
use App\Services\SEGURIDAD_ERP\Finanzas\ConfiguracionRemesaService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class ConfiguracionRemesaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ConfiguracionRemesaService
     */
    protected $service;

    /**
     * @var ConfiguracionRemesaTransformer
     */
    protected $transformer;

    public function __construct(Manager $fractal, ConfiguracionRemesaService $service, ConfiguracionRemesaTransformer $transformer)
    {
        $this->middleware( 'auth:api');
        $this->middleware( 'context');
        $this->middleware('permiso:editar_configuracion_finanzas_remesas');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function actualizar(Request $request){
        $remesa = $this->service->actualizar($request->all());
        return $this->respondWithItem($remesa);
    }

    public function show()
    {
        $remesa = $this->service->show();
        if($remesa){
            return $this->respondWithItem($remesa);
        }
        return null;
    }

}
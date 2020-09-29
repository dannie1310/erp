<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/25/19
 * Time: 6:27 PM
 */

namespace App\Http\Controllers\v1\SEGURIDAD_ERP;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\ConfiguracionObraTransformer;
use App\Services\SEGURIDAD_ERP\ConfiguracionObraService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class ConfiguracionObraController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ConfiguracionObraService
     */
    protected $service;

    /**
     * @var ConfiguracionObraTransformer
     */
    protected $transformer;

    public function __construct(Manager $fractal, ConfiguracionObraService $service, ConfiguracionObraTransformer $transformer)
    {
        $this->middleware( 'auth:api');
        $this->middleware( 'context')->except(['index']);
        //$this->middleware('permiso:registrar_cuenta_corriente')->only(['update']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function contexto(Request $request)
    {
        return $this->service->contexto();
    }

    public function configuracion(Request $request)
    {
        return $this->respondWithItem($this->service->configuracion($request->all()));
    }
}

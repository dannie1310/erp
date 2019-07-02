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

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
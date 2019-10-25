<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\TipoAreaCompradoraTransformer;
use App\Models\SEGURIDAD_ERP\TipoAreaCompradora;
use App\Services\SEGURIDAD_ERP\AreaCompradoraService;
use App\Traits\ControllerTrait;
use Tymon\JWTAuth\Manager;

class AreaCompradoraController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var AreaCompradoraService
     */
    protected $service;

    /**
     * @var TipoAreaCompradoraTransformer
     */
    protected $transformer;

    public function __construct(Manager $fractal, AreaCompradoraService $service, TipoAreaCompradoraTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer
    }

}

<?php

namespace App\Http\Controllers\v1\CTPQ_NOM;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CTPQ_NOM\PolizaTransformer;
use App\Services\CTPQ_NOM\PolizaService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class PolizaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var PolizaService
     */
    protected $service;

    /**
     * @var PolizaTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param PolizaService $service
     * @param PolizaService $transformer
     */
    public function __construct(Manager $fractal, PolizaService $service, PolizaTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    /**
     * Descargar XML con poliza para IFS
     * @return mixed
     */
    public function descarga(Request $request)
    {
        return $this->service->xml($request->all()['id'], $request->all('empresa'));
    }
}

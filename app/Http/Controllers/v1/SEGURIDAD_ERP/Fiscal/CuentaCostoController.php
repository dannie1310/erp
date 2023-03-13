<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal;

use App\Http\Transformers\SEGURIDAD_ERP\Fiscal\CuentaCostoTransformer;
use App\Services\SEGURIDAD_ERP\InformeCostoVsCFDI\CuentaCostoService;
use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;

class CuentaCostoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var CuentaCostoService
     */
    protected $service;

    /**
     * @var CuentaCostoTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param CuentaCostoService $service
     * @param CuentaCostoTransformer $transformer
     */
    public function __construct(Manager $fractal, CuentaCostoService $service, CuentaCostoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function cargarPorLayout(Request $request){
        return $this->service->cargarPorLayout($request->all());
    }

}

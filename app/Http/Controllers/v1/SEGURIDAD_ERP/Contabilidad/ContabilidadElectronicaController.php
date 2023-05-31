<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad;

use App\Http\Controllers\Controller;
use App\Services\SEGURIDAD_ERP\Contabilidad\ContabilidadElectronicaService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class ContabilidadElectronicaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ContabilidadElectronicaService
     */
    protected $service;

    /**
     * @param Manager $fractal
     * @param ContabilidadElectronicaService $service
     */
    public function __construct(Manager $fractal, ContabilidadElectronicaService $service)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
    }

    public function getDatosXML(Request $request)
    {
        return response()->json($this->service->getDatosXML($request->all()), 200);
    }
}

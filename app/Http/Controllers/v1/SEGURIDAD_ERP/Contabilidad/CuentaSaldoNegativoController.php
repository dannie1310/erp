<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad;

use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\CuentaSaldoNegativoTransformer;
use App\Services\SEGURIDAD_ERP\Contabilidad\CuentaSaldoNegativoService;
use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;


class CuentaSaldoNegativoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var CuentaSaldoNegativoService
     */
    protected $service;

    /**
     * @var CuentaSaldoNegativoTransformer
     */
    protected $transformer;

    /**
     * CuentaSaldoNegativoController constructor.
     * @param Manager $fractal
     * @param CuentaSaldoNegativoService $service
     * @param CuentaSaldoNegativoTransformer $transformer
     */
    public function __construct(Manager $fractal, CuentaSaldoNegativoService $service, CuentaSaldoNegativoTransformer $transformer)
    {
        // $this->middleware( 'auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function sincronizar(Request $request)
    {
        $res = $this->service->sincronizar();
        return response()->json($res, 200);
    }

    public function obtenerInforme(Request $request, $id)
    {
        $respuesta =$this->service->obtenerInforme($id);
        return response()->json($respuesta, 200);
    }

    public function obtenerInformeMovimientos(Request $request, $id)
    {
        $data = $request->all();
        $respuesta =$this->service->obtenerInformeMovimientos($data);
        return response()->json($respuesta, 200);
    }
}

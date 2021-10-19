<?php


namespace App\Http\Controllers\v1\CTPQ;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CTPQ\CuentaTransformer;
use App\Services\CTPQ\CuentaService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class CuentaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var CuentaService
     */
    protected $service;

    /**
     * @var CuentaTransformer
     */
    protected $transformer;

    /**
     * CuentaController constructor.
     * @param Manager $fractal
     * @param CuentaService $service
     * @param CuentaTransformer $transformer
     */
    public function __construct(Manager $fractal, CuentaService $service, CuentaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('permiso:asociar_cuentas_contpaq_con_proveedor')->only(['paginate','asociarCuenta']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function asociarCuenta(Request $request){
        $resp = $this->service->asociarCuenta($request->all());
        return $this->respondWithItem($resp);
    }

    public function asociarProveedor(Request $request){
        $resp = $this->service->solicitaAsociacionProveedor($request->all());
        return [];
    }
}

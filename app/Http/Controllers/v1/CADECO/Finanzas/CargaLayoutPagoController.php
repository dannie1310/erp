<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 03:58 PM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\CargaLayoutPagoTransformer;
use App\Services\CADECO\Finanzas\CargaLayoutPagoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use App\Http\Requests\Finanzas\StoreCargaLayoutPagoRequest;

class CargaLayoutPagoController extends Controller
{
    use ControllerTrait{
        store as protected traitStore;
    }

    /**
     * @var CargaLayoutPagoService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var CargaLayoutPagoTransformer
     */
    protected $transformer;

    /**
     * CargaLayoutPagoController constructor.
     * @param CargaLayoutPagoService $service
     * @param Manager $fractal
     * @param CargaLayoutPagoTransformer $transformer
     */
    public function __construct(CargaLayoutPagoService $service, Manager $fractal, CargaLayoutPagoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_carga_layout_pago')->only(['paginate','show']);
        $this->middleware('permiso:registrar_carga_layout_pago')->only(['store']);

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function presentaPagos(Request $request)
    {
        $respuesta = $this->service->validarLayout($request->pagos);
        return response()->json($respuesta, 200);
    }

    public function store(StoreCargaLayoutPagoRequest $request)
    {
        return $this->traitStore($request);
    }

    public function autorizar($data)
    {
        return $this->service->autorizar($data);
    }
}

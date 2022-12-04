<?php


namespace App\Http\Controllers\v1\SEGUIMIENTO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGUIMIENTO\FacturaTransformer;
use App\Services\SEGUIMIENTO\Finanzas\FacturaService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class FacturaController extends Controller
{
    use ControllerTrait;

    /**
     * @var FacturaService
     */
    protected $service;

    /**
     * @var FacturaTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * FacturaController constructor.
     * @param FacturaService $service
     * @param FacturaTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(FacturaService $service, FacturaTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->middleware('permisoGlobal:cancelar_factura_cuenta_x_cobrar')->only('cancelar');
        $this->middleware('permisoGlobal:consultar_factura_cuenta_x_cobrar')->only(['show','paginate','index','find']);
        $this->middleware('permisoGlobal:registrar_factura_cuenta_x_cobrar')->only(['store','cargarArchivo']);

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }

    public function cancelar(Request $request , $id){
        $item = $this->service->cancelar($request->all(),$id);
        return $this->respondWithItem($item);
    }

    public function cargarArchivo(Request $request){
        $respuesta = $this->service->cargarArchivo($request);
        return response()->json($respuesta, 200);
    }
}

<?php


namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\CotizacionTransformer;
use App\Services\CADECO\Compras\CotizacionService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class CotizacionController extends Controller
{
    use ControllerTrait {
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CotizacionService
     */
    private $service;

    /**
     * @var CotizacionTransformer
     */
    private $transformer;

    /**
     * SalidaAlmacenController constructor.
     * @param Manager $fractal
     * @param CotizacionService $service
     * @param CotizacionTransformer $transformer
     */

    public function __construct(Manager $fractal, CotizacionService $service, CotizacionTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:registrar_cotizacion_compra')->only(['store']);
        $this->middleware('permiso:consultar_cotizacion_compra')->only(['show','paginate','index','find']);
        $this->middleware('permiso:editar_cotizacion_compra')->only(['update']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function descargaLayout($id)
    {
        return $this->service->descargaLayout($id);
    }

    public function cargaLayout(Request $request)
    {
        $x = 0;

        while($x <= 8)
        {
            $ret[] = array(
                'i' => 1,
                'material' => 'qwe'.$x,
                'numero_parte' => 12345,
                'no_parte_equi' => '12Koo',
                'pag' => NULL,
                'descripcion' => 'JORGE ARMANDO',
                'unidad' => 'PZA',
                'ref' => 'REFFFF',
                'fecha' => '2020-02-02',
                'cantidad' => 20,
                'observaciones' => 'GIORGIO',
                'clave_concepto' => ''
            );
            $x++;
        }

        // dd($request);

        // abort(400,'No se puede procesar la Requisición');
         $res = $this->service->cargaLayout($request->file, $request->id, $request->name);
         dd('parar de regreso en servicio');
        return response()->json($res, 200);
    }
}
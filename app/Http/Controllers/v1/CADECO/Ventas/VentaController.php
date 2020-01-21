<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/12/2019
 * Time: 08:13 PM
 */

namespace App\Http\Controllers\v1\CADECO\Ventas;


use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\CancelarVentaRequest;
use App\Services\CADECO\Ventas\VentaService;
use App\Http\Transformers\CADECO\Ventas\VentaTransformer;

class VentaController extends Controller
{
    use ControllerTrait{
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var VentaService
     */
    private $service;

    /**
     * @var VentaTransformer
     */
    private $transformer;

    /**
     * VentaController constructor.
     * @param Manager $fractal
     * @param VentaService $service
     * @param VentaTransformer $transformer
     */
    public function __construct(Manager $fractal, VentaService $service, VentaTransformer $transformer)
    {
        $this->middleware('addAccessToken')->only('pdfVenta');
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_venta')->only(['show','paginate','index','find']);
        $this->middleware('permiso:cancelar_venta')->only(['destroy']);
        $this->middleware('permiso:registrar_venta')->only(['store']);


        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function destroy(CancelarVentaRequest $request, $id)
    {
        return $this->traitDestroy($request, $id);
    }

    public function pdfVenta($id)
    {
        if (auth()->user()->can('consultar_venta')) {
            return $this->service->pdfVenta($id);
        }
        dd('No cuentas con los permisos necesarios para realizar la acción solicitada');
    }

    public function pdfFactura($id){
        if (auth()->user()->can('consultar_venta')) {
            return $this->service->pdfFactura($id);
        }
        dd('No cuentas con los permisos necesarios para realizar la acción solicitada');
    }
}
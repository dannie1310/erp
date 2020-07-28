<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 04:20 PM
 */

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad;

use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionTransformer as Transformer;
use App\Services\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionService as Service;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;
use Illuminate\Http\Request;

class SolicitudEdicionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var Service
     */
    protected $service;

    /**
     * @var Transformer
     */
    protected $transformer;

    public function __construct(Manager $fractal, Service $service, Transformer $transformer)
    {
        // $this->middleware( 'auth:api')->except('impresionPolizas');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function cargaXLS(Request $request)
    {
        $respuesta =$this->service->procesaSolicitudXLS($request->nombre_archivo, $request->solicitud);
        return response()->json($respuesta, 200);
    }

    public function autorizar(Request $request, $id)
    {
        return $this->service->autorizar($id,$request);
    }

    public function rechazar($id)
    {
        return $this->service->rechazar($id);
    }

    public function aplicar($id)
    {
        return $this->service->aplicar($id);
    }

    public function descargarXLS($id)
    {
        return $this->service->descargarXLS($id);
    }

    public function impresionPolizas(Request $request, $id)
    {
        return $this->service->impresionPolizas($id, $request->caida)->create();
    }

    public function impresionPolizasPropuesta($id)
    {
        return $this->service->impresionPolizasPropuesta($id)->create();
    }

    public function impresionPolizasOriginal($id)
    {
        return $this->service->impresionPolizasOriginal($id)->create();
    }
}

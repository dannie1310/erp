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

        $this->middleware('permisoGlobal:consultar_poliza_nominas_ctpq')->only(['show','paginate','index']);
        $this->middleware('permisoGlobal:descargar_xml_poliza_ifs_nomina_ctpq')->only(['descarga']);
        $this->middleware('permisoGlobal:enviar_correo_xml_poliza_ifs_nomina_ctpq')->only(['correo']);

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
        return $this->service->xml($request->all()['id'], $request->all('empresa')['empresa']);
    }

    public function correo(Request $request, $id)
    {
        return $this->service->correo($id,$request->all('id_empresa')['id_empresa']);
    }
}

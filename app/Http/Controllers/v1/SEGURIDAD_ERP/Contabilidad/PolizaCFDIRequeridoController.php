<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad;

use App\Services\SEGURIDAD_ERP\Contabilidad\PolizaCFDIRequeridoService as Service;
use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\PolizaCFDIRequeridoTransformer as Transformer;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;
use Illuminate\Http\Request;

class PolizaCFDIRequeridoController extends Controller
{
    use ControllerTrait {
        show as protected traitShow;
        paginate as protected traitPaginate;
        index as protected traitIndex;
    }

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
        $this->middleware( 'auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function show(Request $request, $id)
    {
        return $this->traitShow($request, $id);
    }

    public function paginate(Request $request)
    {
        return $this->traitPaginate($request);
    }

    public function index(Request $request)
    {
        return $this->traitIndex($request);
    }

    public function descargarXLS(Request $request)
    {
        return $this->service->descargaXLS($request->all());
    }

}

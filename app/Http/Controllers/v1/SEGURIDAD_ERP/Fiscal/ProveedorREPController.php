<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal;


use App\Events\RegistroNotificacionREP;
use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Fiscal\ProveedorREPTransformer;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Notifications\NotificacionREP;
use App\Services\SEGURIDAD_ERP\Fiscal\ProveedorREPService;
use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;


class ProveedorREPController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var Service|ProveedorREPService
     */
    protected $service;

    /**
     * @var Transformer|ProveedorREPTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param ProveedorREPService $service
     * @param ProveedorREPTransformer $transformer
     */
    public function __construct(Manager $fractal, ProveedorREPService $service, ProveedorREPTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function show(Request $request, $id)
    {
        $item = $this->service->show($request->all(), $id);
        return $this->respondWithItem($item);
    }

    public function comunicadoPdf($id)
    {
        return $this->service->comunicadoPdf($id);
    }

    public function descargaXls(Request $request)
    {
        return $this->service->descargaXls($request->all());
    }

    public function enviarComunicado(Request $request, $id)
    {
        return $this->service->enviarComunicado($id, $request->all());
    }

}

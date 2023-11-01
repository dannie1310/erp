<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\RelacionGastoTransformer;
use App\Services\CONTROLRECURSOS\RelacionGastoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class RelacionGastoController extends Controller
{
    use ControllerTrait;

    /**
     * @var RelacionGastoTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var RelacionGastoService
     */
    protected $service;

    /**
     * @param RelacionGastoTransformer $transformer
     * @param Manager $fractal
     * @param RelacionGastoService $service
     */
    public function __construct(RelacionGastoTransformer $transformer, Manager $fractal, RelacionGastoService $service)
    {
        $this->middleware('auth:api');
        $this->middleware('permisoGlobal:consultar_relacion_gastos_recursos')->only(['show','paginate','index','pdfRelacion']);
        $this->middleware('permisoGlobal:registrar_relacion_gastos_recursos')->only('store');
        $this->middleware('permisoGlobal:editar_relacion_gastos_recursos')->only('update');
        $this->middleware('permisoGlobal:abrir_relacion_gastos_recursos')->only('open');
        $this->middleware('permisoGlobal:cerrar_relacion_gastos_recursos')->only('close');
        $this->middleware('permisoGlobal:eliminar_relacion_gastos_recursos')->only('destroy');

        $this->transformer = $transformer;
        $this->fractal = $fractal;
        $this->service = $service;
    }

    public function close(Request $request, $id)
    {
        return $this->respondWithItem($this->service->close($id));
    }

    public function open(Request $request, $id)
    {
        return $this->respondWithItem($this->service->open($id));
    }

    public function pdfRelacion($id)
    {
        return $this->service->pdfRelacion($id);
    }

    public function storeReembolsoXSolicitud(Request $request)
    {
        return $this->respondWithItem($this->service->store($request->all()));
    }
}

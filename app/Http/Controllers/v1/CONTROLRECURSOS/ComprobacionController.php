<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\ComprobacionTransformer;
use App\Services\CONTROLRECURSOS\ComprobacionService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class ComprobacionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ComprobacionTransformer
     */
    protected $transformer;

    /**
     * @var ComprobacionService
     */
    protected $service;

    /**
     * @param Manager $fractal
     * @param ComprobacionTransformer $transformer
     * @param ComprobacionService $service
     */
    public function __construct(Manager $fractal, ComprobacionTransformer $transformer, ComprobacionService $service)
    {
        $this->middleware('auth:api');

        $this->middleware('permisoGlobal:consultar_comprobacion_gastos')->only(['show','paginate','index','pdf']);
        $this->middleware('permisoGlobal:registrar_comprobacion_gastos')->only('store');
        $this->middleware('permisoGlobal:editar_comprobacion_gastos')->only('update');
        $this->middleware('permisoGlobal:abrir_comprobacion_gastos')->only('open');
        $this->middleware('permisoGlobal:cerrar_comprobacion_gastos')->only('close');
        $this->middleware('permisoGlobal:eliminar_comprobacion_gastos')->only('destroy');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }

    public function close(Request $request, $id)
    {
        return $this->respondWithItem($this->service->close($id));
    }

    public function open(Request $request, $id)
    {
        return $this->respondWithItem($this->service->open($id));
    }

    public function pdf($id)
    {
        return $this->service->pdf($id);
    }
}

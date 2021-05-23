<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal;


use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\SEGURIDAD_ERP\Fiscal\FechaInhabilSatService;
use App\Http\Transformers\SEGURIDAD_ERP\Fiscal\FechaInhabilSatTransformer;

class FechaInhabilSatController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var FechaInhabilSatService
     */
    protected $service;

    /**
     * @var FechaInhabilSatTransformer
     */
    protected $transformer;

    /**
     * FechaInhabilSatController constructor.
     * @param Manager $fractal
     * @param FechaInhabilSatService $service
     * @param FechaInhabilSatTransformer $transformer
     */
    public function __construct(Manager $fractal, FechaInhabilSatService $service, FechaInhabilSatTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('permisoGlobal:consultar_fechas_inhabiles_sat')->only(['show','paginate','index','find']);
        $this->middleware('permisoGlobal:registrar_fechas_inhabiles_sat')->only('store');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function delete($id){
        return $this->service->eliminar($id);
    }
}

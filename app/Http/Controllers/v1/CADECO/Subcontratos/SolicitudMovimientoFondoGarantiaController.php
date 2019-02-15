<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 06/02/2019
 * Time: 06:03 PM
 */

namespace App\Http\Controllers\v1\CADECO\Subcontratos;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantiaTransformer;
use App\Services\CADECO\Subcontratos\SolicitudMovimientoFondoGarantiaService;
use App\Traits\ControllerTrait;
use Dingo\Api\Routing\Helpers;

class SolicitudMovimientoFondoGarantiaController extends Controller
{
    use Helpers, ControllerTrait {update as protected traitUpdate; store as protected traitStore; }

    /**
     * @var SolicitudMovimientoFondoGarantiaService
     */
    private $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SolicitudMovimientoFondoGarantiaTransformer
     */
    protected $transformer;

    /**
     * CuentaAlmacenController constructor.
     * @param SolicitudMovimientoFondoGarantiaService $service
     * @param Manager $fractal
     * @param SolicitudMovimientoFondoGarantiaTransformer $transformer
     */
    public function __construct(SolicitudMovimientoFondoGarantiaService $service, Manager $fractal, SolicitudMovimientoFondoGarantiaTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function store(StoreSolicitudMovimientoFondoGarantiaRequest $request)
    {
        return $this->traitStore($request);
    }

    public function aprobar(AprobarSolicitudMovimientoFondoGarantiaRequest $request, $id)
    {
        return $this->traitUpdate($request, $id);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2019
 * Time: 09:09 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contratos;


use App\Http\Controllers\Controller;
use App\Http\Requests\Subcontratos\AjustarSaldoFondoGarantiaRequest;
use App\Http\Transformers\CADECO\SubcontratosFG\FondoGarantiaTransformer;
use App\Services\CADECO\Contratos\FondoGarantia\FondoGarantiaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class FondoGarantiaController extends Controller
{
    use ControllerTrait;
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
    public function __construct(FondoGarantiaService $service, Manager $fractal, FondoGarantiaTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function ajustarSaldo(AjustarSaldoFondoGarantiaRequest $request, $id)
    {
        $item = $this->service->ajustarSaldo($request->all(), $id);
        return $this->respondWithItem($item);
    }

}
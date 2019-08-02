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
use App\Http\Requests\Subcontratos\ShowFondoGarantiaRequest;
use App\Http\Requests\Subcontratos\StoreFondoGarantiaRequest;
use App\Http\Transformers\CADECO\SubcontratosFG\FondoGarantiaTransformer;
use App\Services\CADECO\Contratos\FondoGarantia\FondoGarantiaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class FondoGarantiaController extends Controller
{
    use ControllerTrait {
        store as protected traitStore;
        show as protected traitShow;
        paginate as protected traitPaginate;
        index as protected traitIndex;
    }
    /**
     * @var FondoGarantiaService
     */
    private $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var FondoGarantiaTransformer
     */
    protected $transformer;

    /**
     * FondoGarantiaController constructor.
     * @param FondoGarantiaService $service
     * @param Manager $fractal
     * @param FondoGarantiaTransformer $transformer
     */
    public function __construct(FondoGarantiaService $service, Manager $fractal, FondoGarantiaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_fondo_garantia')->only(['paginate','index']);
        $this->middleware('permiso:generar_fondo_garantia')->only('store');
        $this->middleware('permiso:ajustar_saldo_fondo_garantia')->only('ajustarSaldo');
        $this->middleware ('permiso:consultar_detalle_fondo_garantia')->only('show');


        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function ajustarSaldo(AjustarSaldoFondoGarantiaRequest $request, $id)
    {
        $item = $this->service->ajustarSaldo($request->all(), $id);
        return $this->respondWithItem($item);
    }

    public function store(StoreFondoGarantiaRequest $request)
    {
        return $this->traitStore($request);
    }

    public function show(ShowFondoGarantiaRequest $request, $id)
    {
        return $this->traitShow($request, $id);
    }

    public function paginate(ShowFondoGarantiaRequest $request)
    {
        return $this->traitPaginate($request);
    }

    public function index(ShowFondoGarantiaRequest $request)
    {
        return $this->traitIndex($request);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/09/2019
 * Time: 11:53 AM
 */

namespace App\Http\Controllers\v1\CADECO\Almacenes;


use App\Http\Controllers\Controller;
use App\Http\Requests\Almacenes\StoreAjusteNegativoRequest;
use App\Http\Transformers\CADECO\Almacenes\AjusteNegativoTransformer;
use App\Services\CADECO\Almacenes\AjusteNegativoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class AjusteNegativoController extends Controller
{
    use ControllerTrait
    {
        store as protected traitStore;
    }

    /**
     * @var AjusteNegativoService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var AjusteNegativoTransformer
     */
    protected $transformer;

    /**
     * AjusteNegativoController constructor.
     * @param AjusteNegativoService $service
     * @param Manager $fractal
     * @param AjusteNegativoTransformer $transformer
     */
    public function __construct(AjusteNegativoService $service, Manager $fractal, AjusteNegativoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_ajuste_negativo')->only(['show','paginate','index','find']);
        $this->middleware('permiso:registrar_ajuste_negativo')->only('store');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function store(StoreAjusteNegativoRequest $request)
    {
        return $this->traitStore($request);
    }
}
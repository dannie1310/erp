<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 08:41 PM
 */

namespace App\Http\Controllers\v1\CADECO\Almacenes;


use App\Http\Controllers\Controller;
use App\Http\Requests\Almacenes\StoreAjustePositivoRequest;
use App\Http\Transformers\CADECO\Almacenes\AjustePositivoTransformer;
use App\Services\CADECO\Almacenes\AjustePositivoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class AjustePositivoController extends Controller
{
    use ControllerTrait{
        store as protected traitStore;
    }

    /**
     * @var AjustePositivoService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var AjustePositivoTransformer
     */
    protected $transformer;

    /**
     * AjustePositivoController constructor.
     * @param AjustePositivoService $service
     * @param Manager $fractal
     * @param AjustePositivoTransformer $transformer
     */
    public function __construct(AjustePositivoService $service, Manager $fractal, AjustePositivoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_ajuste_positivo')->only(['show','paginate','index','find']);
        $this->middleware('permiso:registrar_ajuste_positivo')->only('store');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function store(StoreAjustePositivoRequest $request)
    {
        return $this->traitStore($request);
    }
}
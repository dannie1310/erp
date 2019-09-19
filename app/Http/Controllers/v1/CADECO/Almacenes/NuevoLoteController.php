<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/09/2019
 * Time: 01:19 PM
 */

namespace App\Http\Controllers\v1\CADECO\Almacenes;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Almacenes\NuevoLoteTransformer;
use App\Services\CADECO\Almacenes\NuevoLoteService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class NuevoLoteController extends Controller
{
    use ControllerTrait;

    /**
     * @var NuevoLoteService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var NuevoLoteTransformer
     */
    protected $transformer;

    /**
     * NuevoLoteController constructor.
     * @param NuevoLoteService $service
     * @param Manager $fractal
     * @param NuevoLoteTransformer $transformer
     */
    public function __construct(NuevoLoteService $service, Manager $fractal, NuevoLoteTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

//        $this->middleware('permiso:consultar_ajuste_negativo')->only(['show','paginate','index','find']);
//        $this->middleware('permiso:registrar_ajuste_negativo')->only('store');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
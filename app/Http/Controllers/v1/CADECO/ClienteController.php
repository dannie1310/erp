<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 03/01/2020
 * Time: 01:40 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\ClienteTransformer;
use App\Services\CADECO\ClienteService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ClienteController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ClienteService
     */
    protected $service;

    /**
     * @var ClienteTransformer
     */
    protected $transformer;

    /**
     * ClienteController constructor.
     * @param Manager $fractal
     * @param ClienteService $service
     * @param ClienteTransformer $transformer
     */
    public function __construct(Manager $fractal, ClienteService $service, ClienteTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_cliente')->only(['show','paginate','index','find']);
        $this->middleware('permiso:registrar_cliente')->only('store');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
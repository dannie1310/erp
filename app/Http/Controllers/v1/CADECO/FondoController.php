<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 2/14/19
 * Time: 12:57 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\FondoTransformer;
use App\Services\CADECO\FondoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class FondoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var FondoService\
     */
    protected $service;

    /**
     * @var FondoTransformer
     */
    protected $transformer;

    /**
     * FondoController constructor.
     *
     * @param Manager $fractal
     * @param FondoService $service
     * @param FondoTransformer $transformer
     */
    public function __construct(Manager $fractal, FondoService $service, FondoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:registrar_fondos')->only(['store']);
        $this->middleware('permiso:consultar_fondos')->only(['paginate','index','show']);


        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}
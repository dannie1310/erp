<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 2/18/19
 * Time: 7:25 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contabilidad\TipoCuentaMaterialTransformer;
use App\Services\CADECO\Contabilidad\TipoCuentaMaterialService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TipoCuentaMaterialController extends Controller
{
    use ControllerTrait;

    /**
     * @var TipoCuentaMaterialService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TipoCuentaMaterialTransformer
     */
    protected $transformer;

    /**
     * TipoCuentaMaterialController constructor.
     *
     * @param TipoCuentaMaterialService $service
     * @param Manager $fractal
     * @param TipoCuentaMaterialTransformer $transformer
     */
    public function __construct(TipoCuentaMaterialService $service, Manager $fractal, TipoCuentaMaterialTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
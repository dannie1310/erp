<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/01/2019
 * Time: 12:16 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contabilidad\CuentaMaterialTransformer;
use App\Services\CADECO\Contabilidad\CuentaMaterialService;
use App\Traits\ControllerTrait;
use Dingo\Api\Routing\Helpers;
use League\Fractal\Manager;

class CuentaMaterialController extends Controller
{
    use Helpers, ControllerTrait;

    /**
     * @var CuentaMaterialService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CuentaMaterialTransformer
     */
    private $transformer;

    /**
     * CuentaMaterialController constructor.
     * @param CuentaMaterialService $service
     * @param Manager $fractal
     * @param CuentaMaterialTransformer $transformer
     */
    public function __construct(CuentaMaterialService $service, Manager $fractal, CuentaMaterialTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
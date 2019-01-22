<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 16/01/2019
 * Time: 01:16 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contabilidad\CuentaGeneralTransformer;
use App\Services\CADECO\Contabilidad\CuentaGeneralService;
use App\Traits\ControllerTrait;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use League\Fractal\Manager;


class CuentaGeneralController extends Controller
{
    use Helpers, ControllerTrait;

    /**
     * @var CuentaGeneralService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CuentaGeneralTransformer
     */
    private $transformer;

    /**
     * CuentaGeneralController constructor.
     * @param CuentaGeneralService $service
     * @param Manager $fractal
     * @param CuentaGeneralTransformer $transformer
     */
    public function __construct(CuentaGeneralService $service, Manager $fractal, CuentaGeneralTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
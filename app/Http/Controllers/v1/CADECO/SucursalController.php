<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 08/08/2019
 * Time: 7:47 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\SucursalTransformer;
use App\Services\CADECO\SucursalService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SucursalController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SucursalService
     */
    protected $service;

    /**
     * @var SucursalTransformer
     */
    protected $transformer;

    /**
     * SucursalController constructor
     *
     * @param Manager $fractal
     * @param SucursalService $service
     * @param SucursalTransformer $transformer
     */

    public function __construct(Manager $fractal, SucursalService $service, SucursalTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;

    }
}

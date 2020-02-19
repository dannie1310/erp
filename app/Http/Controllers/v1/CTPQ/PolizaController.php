<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:46 AM
 */

namespace App\Http\Controllers\v1\CTPQ;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CTPQ\PolizaTransformer;
use App\Services\CTPQ\PolizaService;
use League\Fractal\Manager;
use App\Traits\ControllerTrait;

class PolizaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var PolizaService
     */
    protected $service;

    /**
     * @var PolizaTransformer
     */
    protected $transformer;

    /**
     * PolizaController constructor.
     *
     * @param Manager $fractal
     * @param PolizaService $service
     * @param PolizaTransformer $transformer
     */
    public function __construct(Manager $fractal, PolizaService $service, PolizaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}
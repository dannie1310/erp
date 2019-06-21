<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 29/05/2019
 * Time: 10:34 AM
 */

namespace App\Http\Controllers\v1\MODULOSSAO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\MODULOSSAO\ControlRemesas\RemesaTransformer;
use App\Services\MODULOSSAO\RemesaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class RemesaController extends Controller
{

    use ControllerTrait;

    /**
     * @var RemesaService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var RemesaTransformer
     */
    private $transformer;

    /**
     * RemesaController constructor.
     * @param RemesaService $service
     * @param Manager $fractal
     * @param RemesaTransformer $transformer
     */
    public function __construct(RemesaService $service, Manager $fractal, RemesaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
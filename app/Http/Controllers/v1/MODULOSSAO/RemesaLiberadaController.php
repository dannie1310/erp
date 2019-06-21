<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 27/05/2019
 * Time: 06:03 PM
 */

namespace App\Http\Controllers\v1\MODULOSSAO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\MODULOSSAO\ControlRemesas\RemesaLiberadaTransformer;
use App\Services\MODULOSSAO\RemesaLiberadaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class RemesaLiberadaController extends Controller
{
    use ControllerTrait;

    /**
     * @var RemesaLiberadaService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var RemesaLiberadaTransformer
     */
    private $transformer;

    /**
     * RemesaLiberadaController constructor.
     * @param RemesaLiberadaService $service
     * @param Manager $fractal
     * @param RemesaLiberadaTransformer $transformer
     */
    public function __construct(RemesaLiberadaService $service, Manager $fractal, RemesaLiberadaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
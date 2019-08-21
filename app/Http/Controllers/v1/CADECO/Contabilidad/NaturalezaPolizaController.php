<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 04/03/2019
 * Time: 01:34 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contabilidad\NaturalezaPolizaTransformer;
use App\Services\CADECO\Contabilidad\NaturalezaPolizaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class NaturalezaPolizaController extends Controller
{
    use ControllerTrait;

    /**
     * @var NaturalezaPolizaService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var NaturalezaPolizaTransformer
     */
    protected $transformer;

    /**
     * NaturalezaPolizaController constructor.
     * @param NaturalezaPolizaService $service
     * @param Manager $fractal
     * @param NaturalezaPolizaTransformer $transformer
     */
    public function __construct(NaturalezaPolizaService $service, Manager $fractal, NaturalezaPolizaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
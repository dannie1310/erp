<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 08:06 p. m.
 */


namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Compras\SolicitudComplementoTransformer;
use App\Services\CADECO\Compras\SolicitudComplementoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SolicitudComplementoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SolicitudComplementoService
     */
    protected $service;

    /**
     * @var SolicitudComplementoTransformer
     */
    protected $transformer;


    /**
     * SolicitudComplementoController constructor
     * @param Manager $fractal
     * @param SolicitudComplementoService $service
     * @param SolicitudComplementoTransformer $transformer
     */

    public function __construct(Manager $fractal, SolicitudComplementoService $service, SolicitudComplementoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}

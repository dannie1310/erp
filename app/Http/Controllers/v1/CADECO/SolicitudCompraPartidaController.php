<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 08:39 p. m.
 */


namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\SolicitudCompraPartidaTransformer;
use App\Services\CADECO\SolicitudCompraPartidaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SolicitudCompraPartidaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SolicitudCompraPartidaService
     */
    protected $service;

    /**
     * @var SolicitudCompraPartidaTransformer
     */
    protected $transformer;


    /**
     * SolicitudCompraPartidaController constructor
     * @param Manager $fractal
     * @param SolicitudCompraPartidaService $service
     * @param SolicitudCompraPartidaTransformer $service
     */

    public function __construct(Manager $fractal, SolicitudCompraPartidaService $service, SolicitudCompraPartidaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}

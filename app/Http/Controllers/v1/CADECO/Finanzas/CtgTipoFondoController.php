<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 23/07/2019
 * Time: 7:37 PM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\CtgTipoFondoTransformer;
use App\Services\CADECO\Finanzas\CtgTipoFondoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CtgTipoFondoController extends Controller
{
    use ControllerTrait;
    /**
     * @var CtgTipoFondoService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CtgTipoFondoTransformer
     */
    private $transformer;
    /**
     * CtgTipoFondoController constructor.
     * @param  CtgTipoFondoService $service
     * @param Manager $fractal
     * @param  CtgTipoFondoTransformer $transformer
     */

    public function __construct(CtgTipoFondoService $service, Manager $fractal, CtgTipoFondoTransformer $transformer){
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
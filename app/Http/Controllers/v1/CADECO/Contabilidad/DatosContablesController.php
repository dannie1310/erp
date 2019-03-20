<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/20/19
 * Time: 12:29 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contabilidad\DatosContablesTransformer;
use App\Services\CADECO\Contabilidad\DatosContablesService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class DatosContablesController extends Controller
{
    use ControllerTrait;

    /**
     * @var DatosContablesService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var DatosContablesTransformer
     */
    private $transformer;

    /**
     * DatosContablesController constructor.
     * @param DatosContablesService $service
     * @param Manager $fractal
     * @param DatosContablesTransformer $transformer
     */
    public function __construct(DatosContablesService $service, Manager $fractal, DatosContablesTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
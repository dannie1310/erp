<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 30/01/19
 * Time: 07:22 PM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Finanzas\TipoMovimientoTransformer;
use App\Services\CADECO\Finanzas\TipoMovimientoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TipoMovimientoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TipoMovimientoService
     */
    protected $service;

    /**
     * @var TipoMovimientoTransformer
     */
    protected $transformer;

    /**
     * TipoMovimientoController constructor.
     * @param Manager $fractal
     * @param TipoMovimientoService $service
     * @param TipoMovimientoTransformer $transformer
     */
    public function __construct(Manager $fractal, TipoMovimientoService $service, TipoMovimientoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: jlopeza
 * Date: 02/06/2020
 * Time: 04:51 PM
 */

namespace App\Http\Controllers\v1\CADECO\Catalogos;


use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\CADECO\Catalogos\UnificacionProveedoreService;
use App\Http\Transformers\CADECO\Catalogos\UnificacionProveedoreTransformer;

class UnificacionProveedoresController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var UnificacionProveedoreService
     */
    protected $service;

    /**
     * @var UnificacionProveedoreTransformer
     */
    protected $transformer;

    /**
     * AlmacenController constructor.
     *
     * @param Manager $fractal
     * @param UnificacionProveedoreService $service
     * @param UnificacionProveedoreTransformer $transformer
     */
    public function __construct(Manager $fractal, UnificacionProveedoreService $service, UnificacionProveedoreTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 10/02/2020
 * Time: 17:50 PM
 */

namespace App\Http\Controllers\v1\CADECO\subcontratosEstimaciones;

use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RetencionLiberacionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;
    /**
     * @var RetencionLiberacionService
     */
    protected $service;

    /**
     * @var RetencionLiberacionTransformer
     */
    protected $transformer;

    /**
     * DescuentoController constructor
     *
     * @param Manager $fractal
     * @param RetencionLiberacionService $service
     * @param RetencionLiberacionTransformer $transformer
     */

    public function __construct(Manager $fractal, RetencionLiberacionService $service, RetencionLiberacionTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:registrar_descuento_estimacion_subcontrato')->only(['store']);
        $this->middleware('permiso:editar_descuento_estimacion_subcontrato')->only(['delete']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
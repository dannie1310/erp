<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 10/02/2020
 * Time: 17:50 PM
 */

namespace App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones;

use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\CADECO\SubcontratosEstimaciones\RetencionService;
use App\Http\Transformers\CADECO\SubcontratosEstimaciones\RetencionTransformer;

class RetencionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;
    /**
     * @var RetencionService
     */
    protected $service;

    /**
     * @var RetencionTransformer
     */
    protected $transformer;

    /**
     * RetencionController constructor
     *
     * @param Manager $fractal
     * @param RetencionService $service
     * @param RetencionTransformer $transformer
     */

    public function __construct(Manager $fractal, RetencionService $service, RetencionTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:registrar_retencion_estimacion_subcontrato')->only(['store']);
        $this->middleware('permiso:eliminar_retencion_estimacion_subcontrato')->only(['delete']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

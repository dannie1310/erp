<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 11/02/2020
 * Time: 11:10 AM
 */

namespace App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones;

use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\CADECO\SubcontratosEstimaciones\RetencionTipoService;
use App\Http\Transformers\CADECO\SubcontratosEstimaciones\RetencionTipoTransformer;

class RetencionTipoController extends Controller
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

    public function __construct(Manager $fractal, RetencionTipoService $service, RetencionTipoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 02/04/2020
 * Time: 05:32 PM
 */

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Reportes;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;
use App\Services\SEGURIDAD_ERP\Reportes\ReporteService as Service;
use App\Http\Transformers\SEGURIDAD_ERP\Reportes\ReporteTransformer as Transformer;
use App\Traits\ControllerTrait;

class ReporteController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var Service
     */
    protected $service;

    /**
     * @var Transformer
     */
    protected $transformer;

    public function __construct(Manager $fractal, Service $service, Transformer $transformer)
    {
        $this->middleware( 'auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}
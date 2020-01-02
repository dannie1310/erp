<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 24/10/2019
 * Time: 06:12 PM
 */


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Compras\CtgTipoTransformer;
use App\Services\SEGURIDAD_ERP\Compras\CtgTipoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CtgTipoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;


    /**
     * @var CtgTipoService
     */
    protected $service;

    /**
     * @var CtgTipoTransformer
     */

    protected $transformer;

    /**
     * CtgTipoController constructor
     * @param Manager $fractal
     * @param CtgTipoService $service
     * @param CtgTipoTransformer $transformer
     *
     */


    public function __construct(Manager $fractal, CtgTipoService $service, CtgTipoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }


}

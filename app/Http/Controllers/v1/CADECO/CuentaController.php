<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 30/01/19
 * Time: 07:43 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\CuentaTransformer;
use App\Services\CADECO\CuentaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CuentaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var CuentaService
     */
    protected $service;

    /**
     * @var CuentaTransformer
     */
    protected $transformer;

    /**
     * CuentaController constructor.
     * @param Manager $fractal
     * @param CuentaService $service
     * @param CuentaTransformer $transformer
     */
    public function __construct(Manager $fractal, CuentaService $service, CuentaTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
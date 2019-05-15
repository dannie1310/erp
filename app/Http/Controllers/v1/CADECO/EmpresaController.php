<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 07/02/19
 * Time: 04:59 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Services\CADECO\EmpresaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class EmpresaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var EmpresaService
     */
    protected $service;

    /**
     * @var EmpresaTransformer
     */
    protected $transformer;

    /**
     * EmpresaController constructor.
     *
     * @param Manager $fractal
     * @param EmpresaService $service
     * @param EmpresaTransformer $transformer
     */
    public function __construct(Manager $fractal, EmpresaService $service, EmpresaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
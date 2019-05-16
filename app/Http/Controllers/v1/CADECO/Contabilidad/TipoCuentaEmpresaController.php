<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 11/02/19
 * Time: 01:07 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contabilidad\TipoCuentaEmpresaTransformer;
use App\Services\CADECO\Contabilidad\TipoCuentaEmpresaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TipoCuentaEmpresaController extends Controller
{
    use ControllerTrait;

    /**
     * @var TipoCuentaEmpresaService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TipoCuentaEmpresaTransformer
     */
    protected $transformer;

    /**
     * TipoCuentaEmpresaController constructor.
     * @param TipoCuentaEmpresaService $service
     * @param Manager $fractal
     * @param TipoCuentaEmpresaTransformer $transformer
     */
    public function __construct(TipoCuentaEmpresaService $service, Manager $fractal, TipoCuentaEmpresaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
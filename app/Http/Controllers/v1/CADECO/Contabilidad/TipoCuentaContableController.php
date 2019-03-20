<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 8/01/19
 * Time: 07:07 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTipoCuentaContableRequest;
use App\Http\Transformers\CADECO\Contabilidad\TipoCuentaContableTransformer;
use App\Services\CADECO\Contabilidad\TipoCuentaContableService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TipoCuentaContableController extends Controller
{
    use ControllerTrait {
        store as protected traitStore;
    }

    /**
     * @var TipoCuentaContableService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TipoCuentaContableTransformer
     */
    protected $transformer;

    /**
     * TipoCuentaContableController constructor.
     * @param TipoCuentaContableService $service
     * @param Manager $fractal
     * @param TipoCuentaContableTransformer $transformer
     */
    public function __construct(TipoCuentaContableService $service, Manager $fractal, TipoCuentaContableTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }


    public function store(StoreTipoCuentaContableRequest $request)
    {
        return $this->traitStore($request);
    }
}
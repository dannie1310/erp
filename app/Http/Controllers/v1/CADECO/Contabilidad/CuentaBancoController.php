<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/02/2019
 * Time: 03:40 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCuentaBancoRequest;
use App\Http\Requests\UpdateCuentaBancoRequest;
use App\Http\Transformers\CADECO\Contabilidad\CuentaBancoTransformer;
use App\Models\CADECO\Contabilidad\TipoCuentaContable;
use App\Services\CADECO\Contabilidad\CuentaBancoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CuentaBancoController extends Controller
{
    use ControllerTrait {
        update as protected traitUpdate;
        store as protected traitStore;
    }

    /**
     * @var CuentaBancoService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CuentaBancoTransformer
     */
    private $transformer;


    /**
     * CuentaBancoController constructor.
     * @param CuentaBancoService $service
     * @param Manager $fractal
     * @param CuentaBancoTransformer $transformer
     */
    public function __construct(CuentaBancoService $service, Manager $fractal, CuentaBancoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function update(UpdateCuentaBancoRequest $request, $id)
    {
        return $this->traitUpdate($request, $id);
    }

    public function store(StoreCuentaBancoRequest $request)
    {
        return $this->traitStore($request);
    }

}
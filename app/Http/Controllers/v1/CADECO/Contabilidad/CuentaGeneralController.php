<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 16/01/2019
 * Time: 01:16 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCuentaGeneralRequest;
use App\Http\Requests\UpdateCuentaGeneralRequest;
use App\Http\Transformers\CADECO\Contabilidad\CuentaGeneralTransformer;
use App\Services\CADECO\Contabilidad\CuentaGeneralService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;


class CuentaGeneralController extends Controller
{
    use ControllerTrait {
        update as protected traitUpdate;
        store as protected traitStore;
    }

    /**
     * @var CuentaGeneralService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CuentaGeneralTransformer
     */
    private $transformer;

    /**
     * CuentaGeneralController constructor.
     * @param CuentaGeneralService $service
     * @param Manager $fractal
     * @param CuentaGeneralTransformer $transformer
     */
    public function __construct(CuentaGeneralService $service, Manager $fractal, CuentaGeneralTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_cuenta_general')->only(['show','paginate','find','index']);
        $this->middleware('permiso:registrar_cuenta_general')->only('store');
        $this->middleware('permiso:editar_cuenta_general')->only('update');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function store(StoreCuentaGeneralRequest $request)
    {
        return $this->traitStore($request);
    }

    public function update(UpdateCuentaGeneralRequest $request, $id)
    {
        return $this->traitUpdate($request, $id);
    }
}
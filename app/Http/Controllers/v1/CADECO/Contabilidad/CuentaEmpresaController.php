<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 29/01/2019
 * Time: 12:27 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCuentaEmpresaRequest;
use App\Http\Requests\UpdateCuentaEmpresaRequest;
use App\Http\Transformers\CADECO\Contabilidad\CuentaEmpresaTransformer;
use App\Services\CADECO\Contabilidad\CuentaEmpresaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CuentaEmpresaController extends Controller
{
    use ControllerTrait {
        update as protected traitUpdate;
        store as protected traitStore;
    }

    /**
     * @var CuentaEmpresaService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CuentaEmpresaTransformer
     */
    private $transformer;

    /**
     * CuentaEmpresaController constructor.
     * @param CuentaEmpresaService $service
     * @param Manager $fractal
     * @param CuentaEmpresaTransformer $transformer
     */
    public function __construct(CuentaEmpresaService $service, Manager $fractal, CuentaEmpresaTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function update(UpdateCuentaEmpresaRequest $request, $id)
    {
        return $this->traitUpdate($request, $id);
    }

    public function store(StoreCuentaEmpresaRequest $request)
    {
        return $this->traitStore($request);
    }
}
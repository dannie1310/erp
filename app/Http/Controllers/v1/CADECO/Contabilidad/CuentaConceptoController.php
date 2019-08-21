<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 2/25/19
 * Time: 1:24 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCuentaConceptoRequest;
use App\Http\Requests\UpdateCuentaConceptoRequest;
use App\Http\Transformers\CADECO\Contabilidad\CuentaConceptoTransformer;
use App\Services\CADECO\Contabilidad\CuentaConceptoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CuentaConceptoController extends Controller
{
    use ControllerTrait {
        store as protected traitStore;
        update as protected traitUpdate;
    }

    /**
     * @var CuentaConceptoService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CuentaConceptoTransformer
     */
    private $transformer;

    public function __construct(CuentaConceptoService $service, Manager $fractal, CuentaConceptoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_cuenta_concepto')->only(['show','paginate','find','index']);
        $this->middleware('permiso:registrar_cuenta_concepto')->only('store');
        $this->middleware('permiso:editar_cuenta_concepto')->only('update');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function store(StoreCuentaConceptoRequest $request)
    {
        return $this->traitStore($request);
    }

    public function update(UpdateCuentaConceptoRequest $request, $id)
    {
        return $this->traitUpdate($request, $id);
    }
}
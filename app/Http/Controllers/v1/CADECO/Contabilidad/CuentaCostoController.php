<?php

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;

use App\Http\Requests\DeleteCuentaCostoRequest;
use App\Http\Requests\StoreCuentaCostoRequest;
use App\Http\Requests\UpdateCuentaCostoRequest;
use App\Http\Transformers\CADECO\Contabilidad\CuentaCostoTransformer;
use App\Services\CADECO\Contabilidad\CuentaCostoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class CuentaCostoController extends Controller
{
    use ControllerTrait {
        store as protected traitStore;
        update as protected traitUpdate;
        destroy as protected traitDestroy;
    }

    /**
     * @var CuentaCostoService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CuentaCostoTransformer
     */
    private $transformer;

    /**
     * CuentaCostoController constructor.
     * @param CuentaCostoService $service
     * @param Manager $fractal
     * @param CuentaCostoTransformer $transformer
     */
    public function __construct(CuentaCostoService $service, Manager $fractal, CuentaCostoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_cuenta_costo')->only(['show','paginate','find','index']);
        $this->middleware('permiso:registrar_cuenta_costo')->only('store');
        $this->middleware('permiso:editar_cuenta_costo')->only('update');
        $this->middleware('permiso:eliminar_cuenta_costo')->only('destroy');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function store(StoreCuentaCostoRequest $request)
    {
        return $this->traitStore($request);
    }

    public function update(UpdateCuentaCostoRequest $request, $id)
    {
        return $this->traitUpdate($request, $id);
    }

    public function destroy(DeleteCuentaCostoRequest $request, $id)
    {
        return $this->traitDestroy($request, $id);
    }
}
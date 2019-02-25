<?php

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;

use App\Http\Requests\StoreCuentaCostoRequest;
use App\Http\Requests\UpdateCuentaCostoRequest;
use App\Http\Transformers\CADECO\Contabilidad\CuentaCostoTransformer;
use App\Services\CADECO\Contabilidad\CuentaCostoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CuentaCostoController extends Controller
{
    use ControllerTrait {
        store as protected traitStore;
        update as protected traitUpdate;
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
        $this->middleware('auth');
        $this->middleware('context');

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
}
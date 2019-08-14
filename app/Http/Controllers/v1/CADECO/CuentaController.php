<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 30/01/19
 * Time: 07:43 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCuentaRequest;
use App\Http\Requests\UpdateCuentaRequest;
use App\Http\Transformers\CADECO\CuentaTransformer;
use App\Services\CADECO\CuentaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CuentaController extends Controller
{
    use ControllerTrait{
        store as protected traitStore;
        update as protected traitUpdate;
    }

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
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:registrar_cuenta_corriente')->only(['store']);
        $this->middleware('permiso:consultar_cuenta_corriente')->only(['paginate','index','show']);
        $this->middleware('permiso:editar_cuenta_corriente')->only('update');


        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function store(StoreCuentaRequest $request)
    {
        return $this->traitStore($request);
    }

    public function update(UpdateCuentaRequest $request, $id)
    {
        return $this->traitUpdate($request, $id);
    }
}

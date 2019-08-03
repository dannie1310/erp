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
use App\Http\Requests\UpdateTipoCuentaContableRequest;
use App\Http\Transformers\CADECO\Contabilidad\TipoCuentaContableTransformer;
use App\Services\CADECO\Contabilidad\TipoCuentaContableService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TipoCuentaContableController extends Controller
{
    use ControllerTrait {
        update as protected traitUpdate;
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
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_tipo_cuenta_contable')->only(['show','paginate','find','index']);
        $this->middleware('permiso:registrar_cuenta_contable_bancaria')->only('store');
        $this->middleware('permiso:editar_tipo_cuenta_contable')->only('update');
        $this->middleware('permiso:eliminar_tipo_cuenta_contable')->only('destroy');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function update(UpdateTipoCuentaContableRequest $request, $id)
    {
        return $this->traitUpdate($request, $id);
    }

    public function store(StoreTipoCuentaContableRequest $request)
    {
        return $this->traitStore($request);
    }
}
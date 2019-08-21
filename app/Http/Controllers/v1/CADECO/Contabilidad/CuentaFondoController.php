<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 11/01/19
 * Time: 05:03 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCuentaFondoRequest;
use App\Http\Requests\UpdateCuentaFondoRequest;
use App\Http\Transformers\CADECO\Contabilidad\CuentaFondoTransformer;
use App\Services\CADECO\Contabilidad\CuentaFondoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;
use Illuminate\Http\Request;

class CuentaFondoController extends Controller
{
    use ControllerTrait {
        update as protected traitUpdate;
        store as protected traitStore;
    }

    /**
     * @var CuentaFondoService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CuentaFondoTransformer
     */
    private $transformer;

    /**
     * CuentaFondoController constructor.
     * @param CuentaFondoService $service
     * @param Manager $fractal
     * @param CuentaFondoTransformer $transformer
     */
    public function __construct(CuentaFondoService $service, Manager $fractal, CuentaFondoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_cuenta_fondo')->only(['show','paginate','find','index']);
        $this->middleware('permiso:registrar_cuenta_fondo')->only('store');
        $this->middleware('permiso:editar_cuenta_fondo')->only('update');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function update(UpdateCuentaFondoRequest $request, $id)
    {
        return $this->traitUpdate($request, $id);
    }

    public function store(StoreCuentaFondoRequest $request)
    {
        return $this->traitStore($request);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 18/12/18
 * Time: 09:20 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCuentaAlmacenRequest;
use App\Http\Requests\UpdateCuentaAlmacenRequest;
use App\Http\Transformers\CADECO\Contabilidad\CuentaAlmacenTransformer;
use App\Services\CADECO\Contabilidad\CuentaAlmacenService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CuentaAlmacenController extends Controller
{
    use ControllerTrait {
        update as protected traitUpdate;
        store as protected traitStore;
    }

    /**
     * @var CuentaAlmacenService
     */
    private $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var CuentaAlmacenTransformer
     */
    protected $transformer;

    /**
     * CuentaAlmacenController constructor.
     * @param CuentaAlmacenService $service
     * @param Manager $fractal
     * @param CuentaAlmacenTransformer $transformer
     */
    public function __construct(CuentaAlmacenService $service, Manager $fractal, CuentaAlmacenTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function update(UpdateCuentaAlmacenRequest $request, $id)
    {
        return $this->traitUpdate($request, $id);
    }

    public function store(StoreCuentaAlmacenRequest $request)
    {
        return $this->traitStore($request);
    }
}
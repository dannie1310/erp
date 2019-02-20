<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/01/2019
 * Time: 12:16 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCuentaMaterialRequest;
use App\Http\Requests\UpdateCuentaMaterialRequest;
use App\Http\Transformers\CADECO\Contabilidad\CuentaMaterialTransformer;
use App\Services\CADECO\Contabilidad\CuentaMaterialService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CuentaMaterialController extends Controller
{
    use ControllerTrait {
        store as protected traitStore;
        update as protected traitUpdate;
    }

    /**
     * @var CuentaMaterialService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CuentaMaterialTransformer
     */
    private $transformer;

    /**
     * CuentaMaterialController constructor.
     * @param CuentaMaterialService $service
     * @param Manager $fractal
     * @param CuentaMaterialTransformer $transformer
     */
    public function __construct(CuentaMaterialService $service, Manager $fractal, CuentaMaterialTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function store(StoreCuentaMaterialRequest $request)
    {
        return $this->traitStore($request);
    }

    public function update(UpdateCuentaMaterialRequest $request, $id)
    {
        return $this->traitUpdate($request, $id);
    }
}
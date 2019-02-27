<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 22/02/2019
 * Time: 04:49 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contabilidad\CierreTransformer;
use App\Services\CADECO\Contabilidad\CierreService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class CierreController extends Controller
{
    use ControllerTrait {
        update as protected traitUpdate;
        store as protected traitStore;
    }

    /**
     * @var CierreService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CierreTransformer
     */
    private $transformer;


    /**
     * CierreController constructor.
     * @param CierreService $service
     * @param Manager $fractal
     * @param CierreTransformer $transformer
     */
    public function __construct(CierreService $service, Manager $fractal, CierreTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function update(UpdateCierreRequest $request, $id)
    {
        return $this->traitUpdate($request, $id);
    }

    public function store(StoreCierreRequest $request)
    {
        return $this->traitStore($request);
    }
}
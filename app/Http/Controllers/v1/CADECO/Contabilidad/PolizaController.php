<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 26/12/18
 * Time: 01:48 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePolizaRequest;
use App\Http\Transformers\CADECO\Contabilidad\PolizaTransformer;
use App\Services\CADECO\Contabilidad\PolizaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class PolizaController extends Controller
{
    use ControllerTrait { update as protected traitupdate; }

    /**
     * @var PolizaService
     */
    private $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var PolizaTransformer
     */
    protected $transformer;

    /**
     * PolizaController constructor.
     * @param PolizaService $service
     * @param Manager $fractal
     * @param PolizaTransformer $transformer
     */
    public function __construct(PolizaService $service, Manager $fractal, PolizaTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function update(UpdatePolizaRequest $request, $id) {
        $this->traitupdate($request, $id);
    }
}
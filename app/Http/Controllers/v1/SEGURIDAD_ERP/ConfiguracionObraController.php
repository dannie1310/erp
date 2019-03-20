<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/12/19
 * Time: 5:05 PM
 */

namespace App\Http\Controllers\v1\SEGURIDAD_ERP;


use App\Http\Controllers\Controller;
use App\Traits\ControllerTrait;

class ConfiguracionObraController extends Controller
{
    use ControllerTrait;

    /**
     * @var ConfiguracionObraService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var ConfiguracionObraTransformer
     */
    private $transformer;

    public function __construct(CierreService $service, Manager $fractal, CierreTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function store(StoreCierreRequest $request)
    {
        return $this->traitStore($request);
    }

    public function cerrar($id)
    {
        $item = $this->service->cerrar($id);
        return $this->respondWithItem($item);
    }

    public function abrir(Request $request, $id)
    {
        $item = $this->service->abrir($request->all(), $id);
        return $this->respondWithItem($item);
    }
}
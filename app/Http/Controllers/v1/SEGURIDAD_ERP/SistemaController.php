<?php

/**
 * Created by PhpStorm.
 * User: Hermes
 * Date: 26/02/2019
 * Time: 05:53 PM
 */
namespace App\Http\Controllers\v1\SEGURIDAD_ERP;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\SistemaTransformer;
use App\Services\SEGURIDAD_ERP\SistemaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;
use Illuminate\Http\Request;


class SistemaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SistemaService
     */
    protected $service;

    /**
     * @var SistemaTransformer
     */
    protected $transformer;

    /**
     * SistemaController constructor.
     *
     * @param Manager $fractal
     * @param SistemaService $service
     * @param SistemaTransformer $transformer
     */
    public function __construct(Manager $fractal, SistemaService $service, SistemaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:habilitar_deshabilitar_sistema')->only('asignacionSistemas');


        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
    public function porObra(Request $request)
    {
        $sistemas = $this->service->porObra($request->all());
        return $this->respondWithCollection($sistemas);
    }
    public function asignacionSistemas(Request $request)
    {
        $response = $this->service->asignacionSistemas($request->all());
        return response()->json($response, 200);
    }
    public function leerAviso($id)
    {
        return $this->service->leerAviso($id);
    }
    public function getAviso($ruta)
    {
        return $this->service->getAviso($ruta);
    }

}

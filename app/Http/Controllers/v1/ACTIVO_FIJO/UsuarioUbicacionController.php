<?php

namespace App\Http\Controllers\v1\ACTIVO_FIJO;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\ACTIVO_FIJO\UsuarioUbicacionService;
use App\Http\Transformers\ACTIVO_FIJO\UsuarioUbicacionTransformer;

class UsuarioUbicacionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var UsuarioUbicacionService
     */
    protected $service;

    /**
     * @var UsuarioUbicacionTransformer
     */
    protected $transformer;

    /**
     * UsuarioUbicacionController constructor.
     *
     * @param Manager $fractal
     * @param UsuarioUbicacionService $service
     * @param UsuarioUbicacionTransformer $transformer
     */
    public function __construct(Manager $fractal, UsuarioUbicacionService $service, UsuarioUbicacionTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function listaUbicaciones(Request $request){
        return $this->service->listaResguardos($request);
    }
}

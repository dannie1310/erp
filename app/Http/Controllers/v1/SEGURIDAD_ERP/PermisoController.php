<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP;

use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\PermisoTransformer;
use App\Services\SEGURIDAD_ERP\PermisoService;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use App\Traits\AuditoriaTrait;


class PermisoController extends Controller
{

    use AuditoriaTrait{
        getAuditoriaPermisoRol as protected traitPermisoRol;
    }
    protected $fractal;

    protected $service;

    protected $transformer;

    public function __construct(Manager $fractal, PermisoService $service, PermisoTransformer $transformer)
    {
        $this->middleware('aunt');
        $this->middleware('context');
        //$this->middleware('esquemaPersonalizado');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function asignacionPersonalizada(Request $request)
    {
        $response = $this->service->asignacionPersonalizada($request->all());
        return response()->json($response, 200);
    }

    public function porRol(Request $request, $role_id)
    {
        $permisos = $this->service->porRol($request->all(), $role_id);
        return $this->respondWithCollection($permisos);
    }

    public function getAuditoriaPermisoRol(/*Request $request*/){
        return $this->traitPermisoRol(/*$request*/);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Garrido
 * Date: 26/03/2019
 * Time: 20:04
 */

namespace App\Http\Controllers\v1\CADECO\Seguridad;

use App\Http\Controllers\Controller;

use App\Services\CADECO\Seguridad\RolService;
use App\Http\Transformers\CADECO\Seguridad\RolTransformer;
use App\Traits\ControllerTrait;
use App\Traits\AuditoriaTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class RolController extends Controller
{
    use ControllerTrait;

    use AuditoriaTrait{
        getAuditoriaRolUsuario as protected traitRolUsuario;
    }


    protected $fractal;

    protected $service;

    protected $transformer;

    /**
     * RolController constructor.
     * @param Manager $fractal
     * @param RolService $service
     * @param RolTransformer $transformer
     */
    public function __construct(Manager $fractal, RolService $service, RolTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');
        $this->middleware('esquemaPersonalizado');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
    public function asignacionPersonalizada(Request $request)
    {
        $response = $this->service->asignacionPersonalizada($request->all());
        return response()->json($response, 200);
    }

    public function porUsuario(Request $request, $user_id)
    {
        $roles = $this->service->porUsuario($request->all(), $user_id);
        return $this->respondWithCollection($roles);
    }

    public function getAuditoriaRolUsuario(/*Request $request*/){
        return $this->traitRolUsuario(/*$request*/);
    }
}
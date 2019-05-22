<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/25/19
 * Time: 6:27 PM
 */

namespace App\Http\Controllers\v1\SEGURIDAD_ERP;


use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRolRequest;
use App\Http\Requests\DeleteRolRequest;
use App\Http\Transformers\SEGURIDAD_ERP\RolTransformer;
use App\Services\SEGURIDAD_ERP\RolService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class RolController extends Controller
{
    use ControllerTrait {
        store as traitStore;
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var RolService
     */
    protected $service;

    /**
     * @var RolTransformer
     */
    protected $transformer;

    public function __construct(Manager $fractal, RolService $service, RolTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('esquemaGlobal');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function asignacionMasiva(Request $request)
    {
        $response = $this->service->asignacionMasiva($request->all());
        return response()->json($response, 200);
    }

    public function desasignacionMasiva(Request $request)
    {
        $response = $this->service->desasignacionMasiva($request->all());
        return response()->json($response, 200);
    }

    public function asignacionPermisos(Request $request)
    {
        $response = $this->service->asignacionPermisos($request->all());
        return response()->json($response, 200);
    }

    public function porUsuario(Request $request, $user_id)
    {
        $roles = $this->service->porUsuario($request->all(), $user_id);
        return $this->respondWithCollection($roles);
    }

    public function store(CreateRolRequest $request)
    {
        return $this->traitStore($request);
    }

    public function destroy(DeleteRolRequest $request, $id)
    {
        return $this->traitDestroy($request, $id);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/25/19
 * Time: 6:27 PM
 */

namespace App\Http\Controllers\v1\SEGURIDAD_ERP;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\RolTransformer;
use App\Services\SEGURIDAD_ERP\RolService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class RolController extends Controller
{
    use ControllerTrait;

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
        $this->middleware('auth');
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

    public function porUsuario(Request $request, $user_id)
    {
        $roles = $this->service->porUsuario($request->all(), $user_id);
        return $this->respondWithCollection($roles);
    }
}
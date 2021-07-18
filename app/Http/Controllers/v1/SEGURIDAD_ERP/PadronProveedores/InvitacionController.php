<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;

use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\InvitacionTransformer;
use App\Services\SEGURIDAD_ERP\PadronProveedores\InvitacionService;

class InvitacionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var InvitacionService
     */
    protected $service;

    /**
     * @var InvitacionTransformer
     */
    protected $transformer;

    /**
     * InvitacionController constructor.
     * @param Manager $fractal
     * @param InvitacionService $service
     * @param InvitacionTransformer $transformer
     */
    public function __construct(Manager $fractal, InvitacionService $service, InvitacionTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function getPorCotizar(Request $request)
    {
        $collection = $this->service->getPorCotizar($request->all());
        return $this->respondWithCollection($collection);
    }

}

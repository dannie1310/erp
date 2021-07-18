<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;

use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\InvitacionTransformer as Transformer;
use App\Services\SEGURIDAD_ERP\PadronProveedores\InvitacionService as Service;

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
     * @param Service $service
     * @param Transformer $transformer
     */
    public function __construct(Manager $fractal, Service $service, Transformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context')->only("store");

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function store(Request $request){
        $sucursal = $this->service->store($request->all());
        return $this->respondWithItem($sucursal);
    }

    public function getPorCotizar(Request $request)
    {
        $collection = $this->service->getPorCotizar($request->all());
        return $this->respondWithCollection($collection);
    }
}

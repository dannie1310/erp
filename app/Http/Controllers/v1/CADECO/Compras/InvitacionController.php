<?php


namespace App\Http\Controllers\v1\CADECO\Compras;


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
     * @var
     */
    protected $service;

    /**
     * @var
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
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function store(Request $request){
        $invitaciones = $this->service->store($request->all());
        return $this->respondWithCollection($invitaciones);
    }

    public function storeContraoferta(Request $request){
        $invitaciones = $this->service->storeContraoferta($request->all());
        return $this->respondWithCollection($invitaciones);
    }

    public function abrir($id)
    {
        $obj = $this->service->show($id);
        $obj->abierta = 1;
        $obj->save();
        return response()->json("Gracias por confirmar la recepción de la invitación", 200);
    }

    public function pdf($id)
    {
        return $this->service->pdf($id);
    }
}

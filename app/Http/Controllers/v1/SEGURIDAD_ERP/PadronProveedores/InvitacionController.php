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
        $this->middleware('permisoGlobal:consultar_cotizacion_proveedor')->only(['getPorCotizar','getSolicitud']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function store(Request $request){
        $invitacion = $this->service->store($request->all());
        return $this->respondWithItem($invitacion);
    }

    public function abrir($id)
    {
        $obj = $this->service->show($id);
        $obj->abierta = 1;
        $obj->save();
        return response()->json("Gracias por confirmar la recepción de la invitación", 200);
    }

    public function getPorCotizar(Request $request)
    {
        return $this->service->getPorCotizar($request->all());
    }

    public function getSolicitud(Request $request, $id)
    {
        return $this->service->getSolicitud($id);
    }
}

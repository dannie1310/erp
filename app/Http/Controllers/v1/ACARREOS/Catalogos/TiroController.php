<?php


namespace App\Http\Controllers\v1\ACARREOS\Catalogos;


use App\Facades\Context;
use App\Http\Controllers\Controller;
use App\Http\Transformers\ACARREOS\Catalogos\TiroTransformer;
use App\Services\ACARREOS\Catalogos\TiroService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;

class TiroController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TiroService
     */
    protected $service;

    /**
     * @var TiroTransformer
     */
    protected $transformer;

    /**
     * TiroController constructor.
     * @param Manager $fractal
     * @param TiroService $service
     * @param TiroTransformer $transformer
     */
    public function __construct(Manager $fractal, TiroService $service, TiroTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_tiro')->only(['show','paginate','index','find','descargaTiros']);
        $this->middleware('permiso:editar_tiro')->only(['asignarConcepto']);
        $this->middleware('permiso:registrar_tiro')->only(['store']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function asignarConcepto(Request $request, $id)
    {
        return $this->respondWithItem($this->service->asignarConcepto($request->all(), $id));
    }

    public function activar(Request $request, $id)
    {
        return $this->respondWithItem($this->service->activar($id));
    }

    public function desactivar(Request $request, $id)
    {
        return $this->respondWithItem($this->service->desactivar($request->all(),$id));
    }

    public function descargaTiros()
    {
        return $this->service->excel();
    }
}

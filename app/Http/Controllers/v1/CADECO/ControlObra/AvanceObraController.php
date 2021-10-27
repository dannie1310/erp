<?php


namespace App\Http\Controllers\v1\CADECO\ControlObra;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\ControlObra\AvanceObraTransformer;
use App\Services\CADECO\ControlObra\AvanceObraService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class AvanceObraController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var AvanceObraService
     */
    protected $service;

    /**
     * @var AvanceObraTransformer
     */
    protected $transformer;

    /**
     * AvanceObraController constructor.
     * @param Manager $fractal
     * @param AvanceObraService $service
     * @param AvanceObraTransformer $transformer
     */
    public function __construct(Manager $fractal, AvanceObraService $service, AvanceObraTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_avance_obra')->only(['show','paginate','index','find']);
        $this->middleware('permiso:registrar_avance_obra')->only(['store']);
        $this->middleware('permiso:aprobar_avance_obra')->only(['aprobar']);
        $this->middleware('permiso:revertir_avance_obra')->only(['revertir']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function aprobar(Request $request, $id)
    {
        return $this->service->aprobar($id);
    }

    public function revertir($id)
    {
        return $this->service->revertir($id);
    }
}

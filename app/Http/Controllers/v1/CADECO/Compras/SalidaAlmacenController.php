<?php


namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Requests\Compras\DeleteSalidaAlmacenRequest;
use App\Http\Transformers\CADECO\Compras\SalidaAlmacenTransformer;
use App\Services\CADECO\Compras\SalidaAlmacenService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SalidaAlmacenController extends Controller
{
    use ControllerTrait {
        destroy as traitDestroy;
    }

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var SalidaAlmacenService
     */
    private $service;

    /**
     * @var SalidaAlmacenTransformer
     */
    private $transformer;

    /**
     * SalidaAlmacenController constructor.
     * @param Manager $fractal
     * @param SalidaAlmacenService $service
     * @param SalidaAlmacenTransformer $transformer
     */

    public function __construct(Manager $fractal, SalidaAlmacenService $service, SalidaAlmacenTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_salida_almacen')->only(['show','paginate','index','find']);
        $this->middleware('permiso:eliminar_salida_almacen')->only(['destroy']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function destroy(DeleteSalidaAlmacenRequest $request, $id)
    {
        return $this->traitDestroy($request, $id);
    }

}
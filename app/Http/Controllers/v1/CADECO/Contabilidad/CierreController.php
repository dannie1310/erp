<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 22/02/2019
 * Time: 04:49 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCierreRequest;
use App\Http\Requests\UpdateCierreRequest;
use App\Http\Transformers\CADECO\Contabilidad\CierreTransformer;
use App\Services\CADECO\Contabilidad\CierreService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class CierreController extends Controller
{
    use ControllerTrait {
        update as protected traitUpdate;
        store as protected traitStore;
    }

    /**
     * @var CierreService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CierreTransformer
     */
    private $transformer;


    /**
     * CierreController constructor.
     * @param CierreService $service
     * @param Manager $fractal
     * @param CierreTransformer $transformer
     */
    public function __construct(CierreService $service, Manager $fractal, CierreTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_cierre_periodo')->only(['show','paginate','find','index']);
        $this->middleware('permiso:editar_cierre_periodo')->only(['abrir','cerrar']);
        $this->middleware('permiso:generar_cierre_periodo')->only('store');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function store(StoreCierreRequest $request)
    {
        return $this->traitStore($request);
    }

    public function cerrar($id)
    {
        $item = $this->service->cerrar($id);
        return $this->respondWithItem($item);
    }

    public function abrir(Request $request, $id)
    {
        $item = $this->service->abrir($request->all(), $id);
        return $this->respondWithItem($item);
    }
}
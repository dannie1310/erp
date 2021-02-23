<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 30/01/20
 * Time: 12:10 PM
 */

namespace App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones;

use App\Http\Transformers\CADECO\SubcontratosEstimaciones\DescuentoTransformer;
use App\Services\CADECO\SubcontratosEstimaciones\DescuentoService;
use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DescuentoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;
    /**
     * @var DescuentoService
     */
    protected $service;

    /**
     * @var DescuentoTransformer
     */
    protected $transformer;

    /**
     * DescuentoController constructor
     *
     * @param Manager $fractal
     * @param DescuentoService $service
     * @param DescuentoTransformer $transformer
     */

    public function __construct(Manager $fractal, DescuentoService $service, DescuentoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:registrar_descuento_estimacion_subcontrato')->only(['storeItem']);
        $this->middleware('permiso:editar_descuento_estimacion_subcontrato')->only(['updateList']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function list($id){
        $lista = $this->service->list($id);
        return $this->respondWithCollection($lista);
    }

    public function listItems(Request $request, $id){
        $lista = $this->service->listItems($id, $request->all());
        return response()->json($lista, 200);
    }

    public function updateList(Request $request){
        $respuesta = $this->service->updateList($request->all());
        return $this->respondWithCollection($respuesta);
    }

    public function storeItem(Request $request){
        $respuesta = $this->service->storeItem($request->all());
        return $this->respondWithCollection($respuesta);
    }
}

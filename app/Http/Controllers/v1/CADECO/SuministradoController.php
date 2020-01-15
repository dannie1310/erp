<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 16/23/20
 * Time: 05:33 PM
 */

namespace App\Http\Controllers\v1\CADECO;

use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\CADECO\SuministradoService;
use App\Http\Transformers\CADECO\SuministradosTransformer;

class SuministradoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SuministradoService
     */
    protected $service;

    /**
     * @var SuministradoTransformer
     */
    protected $transformer;

    /**
     * MonedaController constructor.
     * @param Manager $fractal
     * @param MonedaService $service
     * @param MonedaTransformer $transformer
     */
    public function __construct(Manager $fractal, SuministradoService $service, SuministradosTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:eliminar_material_proveedor')->only(['destroy']);
        $this->middleware('permiso:registrar_material_proveedor')->only(['store']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
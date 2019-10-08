<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 14/02/19
 * Time: 09:06 PM
 */

namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Compras\MaterialTransformer;
use App\Services\CADECO\Compras\MaterialService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class MaterialController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var MaterialService
     */
    protected $service;

    /**
     * @var MaterialTransformer
     */
    protected $transformer;

    /**
     * MaterialController constructor.
     * @param Manager $fractal
     * @param MaterialService $service
     * @param MaterialTransformer $transformer
     */
    public function __construct(Manager $fractal,MaterialService $service, MaterialTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_insumo_material')->only(['show','paginate','index','find']);
        $this->middleware('permiso:registrar_insumo_material')->only('store');


        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:46 AM
 */

namespace App\Http\Controllers\v1\CTPQ;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CTPQ\EmpresaTransformer;
use App\Services\CTPQ\EmpresaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var EmpresaService
     */
    protected $service;

    /**
     * @var EmpresaTransformer
     */
    protected $transformer;

    /**
     * EmpresaController constructor.
     *
     * @param Manager $fractal
     * @param EmpresaService $service
     * @param EmpresaTransformer $transformer
     */
    public function __construct(Manager $fractal, EmpresaService $service, EmpresaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('accesoEmpresaContpaq')->only(['conectar']);


        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function conectar(Request $request){
        return $this->service->conectar($request->id);
    }
}

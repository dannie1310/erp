<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 07/08/19
 * Time: 06:10 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\BancoTransformer;
use App\Services\CADECO\BancoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class BancoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;
    /**
     * @var BancoService
     */
    protected $service;

    /**
     * @var BancoTransformer
     */
    protected $transformer;

    /**
     * BancoController constructor
     *
     * @param Manager $fractal
     * @param BancoService $service
     * @param BancoTransformer $transformer
     */

    public function __construct(Manager $fractal, BancoService $service, BancoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:registrar_banco')->only(['store']);
        $this->middleware('permiso:consultar_banco')->only(['paginate','index','show']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

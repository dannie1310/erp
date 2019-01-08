<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 06:22 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contabilidad\TipoPolizaContpaqTransformer;
use App\Services\CADECO\Contabilidad\TipoPolizaContpaqService;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class TipoPolizaContpaqController extends Controller
{
    /**
     * @var TipoPolizaContpaqService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * TipoPolizaContpaqController constructor.
     * @param TipoPolizaContpaqService $service
     */
    public function __construct(TipoPolizaContpaqService $service, Manager $fractal)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
    }

    public function index() {
        $data = $this->service->index();
        $resource = new Collection($data, new TipoPolizaContpaqTransformer);
        $response = $this->fractal->createData($resource)->toArray();
        return response()->json($response, 200);
    }
}
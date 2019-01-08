<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 06:10 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contabilidad\EstatusPrepolizaTransformer;
use App\Services\CADECO\Contabilidad\EstatusPrepolizaService;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class EstatusPrepolizaController extends Controller
{
    /**
     * @var EstatusPrepolizaService
     */
    private $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * PolizaController constructor.
     * @param PolizaService $service
     * @param Manager $fractal
     */
    public function __construct(EstatusPrepolizaService $service, Manager $fractal)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
    }

    public function index()
    {
        $data = $this->service->index();
        $resource = new Collection($data, new EstatusPrepolizaTransformer);
        $response = $this->fractal->createData($resource)->toArray();
        return response()->json($response, 200);
    }
}
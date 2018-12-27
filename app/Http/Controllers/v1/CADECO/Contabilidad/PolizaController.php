<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 26/12/18
 * Time: 01:48 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contabilidad\PolizaTransformer;
use App\Services\CADECO\Contabilidad\PolizaService;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class PolizaController extends Controller
{
    /**
     * @var PolizaService
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
    public function __construct(PolizaService $service, Manager $fractal)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
    }

    public function index(Request $request)
    {
        $paginator = $this->service->index($request->all());

        $data = $paginator->getCollection();

        $resource = new Collection($data, new PolizaTransformer);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));


        if ($request->has('include')) {
            $this->fractal->parseIncludes($request->get('include'));
        }

        $response = $this->fractal->createData($resource)->toArray();

        return response()->json($response, 200);
    }
}
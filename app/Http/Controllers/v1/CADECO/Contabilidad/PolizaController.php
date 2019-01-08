<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 26/12/18
 * Time: 01:48 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contabilidad\EstatusPrepolizaTransformer;
use App\Http\Transformers\CADECO\Contabilidad\PolizaTransformer;
use App\Services\CADECO\Contabilidad\PolizaService;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;

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

    public function paginate(Request $request)
    {
        $paginator = $this->service->paginate($request->all());

        $data = $paginator->getCollection();

        $resource = new Collection($data, new PolizaTransformer);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));


        if ($request->has('include')) {
            $this->fractal->parseIncludes($request->get('include'));
        }

        $response = $this->fractal->createData($resource)->toArray();

        return response()->json($response, 200);
    }

    public function find(Request $request, $id) {
        $data = $this->service->find($id);
        $resource = new Item($data, new PolizaTransformer);

        if ($request->has('include')) {
            $this->fractal->parseIncludes($request->get('include'));
        }

        $this->fractal->setSerializer(new ArraySerializer());

        $response = $this->fractal->createData($resource)->toArray();
        return response()->json($response, 200);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 18/12/18
 * Time: 09:20 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contabilidad\CuentaAlmacenTransformer;
use App\Services\CADECO\Contabilidad\CuentaAlmacenService;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class CuentaAlmacenController extends Controller
{
    use Helpers;

    /**
     * @var CuentaAlmacenService
     */
    private $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * CuentaAlmacenController constructor.
     * @param CuentaAlmacenService $service
     */
    public function __construct(CuentaAlmacenService $service, Manager $manager)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $manager;
    }

    public function paginate(Request $request)
    {
        $paginator = $this->service->paginate($request->all());
        $data = $paginator->getCollection();

        $resource = new Collection($data, new CuentaAlmacenTransformer);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));


        if ($request->has('include')) {
            $this->fractal->parseIncludes($request->get('include'));
        }

        $response = $this->fractal->createData($resource)->toArray();

        return response()->json($response, 200);
    }

    public function find(Request $request, $id)
    {
        $item = $this->service->find($id);
        $resource = new Item($item, new CuentaAlmacenTransformer);

        if ($request->has('include')) {
            $this->fractal->parseIncludes($request->get('include'));
        }

        $response = $this->fractal->createData($resource)->toArray();

        return response()->json($response, 200);

    }

    public function update(Request $request, $id) {
        $item = $this->service->update($request->all(), $id);

        $resource = new Item($item, new CuentaAlmacenTransformer);

        if ($request->has('include')) {
            $this->fractal->parseIncludes($request->get('include'));
        }

        $response = $this->fractal->createData($resource)->toArray();

        return response()->json($response, 200);
    }
}
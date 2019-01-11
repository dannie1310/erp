<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 11/01/19
 * Time: 12:12 PM
 */

namespace App\Traits;


use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;

trait ControllerTrait
{
    public function paginate(Request $request)
    {
        $paginator = $this->service->paginate($request->all());
        $data = $paginator->getCollection();

        $resource = new Collection($data, $this->transformer);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));


        if ($request->has('include')) {
            $this->fractal->parseIncludes($request->get('include'));
        }

        $this->fractal->setSerializer(new ArraySerializer);
        $response = $this->fractal->createData($resource)->toArray();
        return response()->json($response, 200);
    }

    public function find(Request $request, $id) {
        $data = $this->service->find($id);
        $resource = new Item($data, $this->transformer);

        if ($request->has('include')) {
            $this->fractal->parseIncludes($request->get('include'));
        }

        $this->fractal->setSerializer(new ArraySerializer);
        $response = $this->fractal->createData($resource)->toArray();
        return response()->json($response, 200);
    }

    private function respondItem($item) {
        $resource = new Item($item, $this->transformer);

        if (request()->has('include')) {
            $this->fractal->parseIncludes(request()->get('include'));
        }

        $this->fractal->setSerializer(new ArraySerializer);
        $response = $this->fractal->createData($resource)->toArray();
        return response()->json($response, 200);
    }
}
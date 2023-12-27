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
    public function index(Request $request)
    {
        $collection = $this->service->index($request->all());
        return $this->respondWithCollection($collection);
    }

    public function paginate(Request $request)
    {
        $paginator = $this->service->paginate($request->all());
        return $this->respondWithPaginator($paginator);
    }

    public function show(Request $request, $id)
    {
        $item = $this->service->show($id);
        if($item) {
            return $this->respondWithItem($item);
        }
        return response()->json("{}", 200);;
    }

    public function update(Request $request, $id)
    {
        $item = $this->service->update($request->all(), $id);
        return $this->respondWithItem($item);
    }

    public function destroy(Request $request, $id)
    {
        $this->service->delete($request->all(), $id);
        return response()->json("{}", 200);
    }

    private function respondWithCollection($collection)
    {
        $resource = new Collection($collection, $this->transformer);

        $this->parseIncludes();
        $this->fractal->setSerializer(new ArraySerializer);
        $response = $this->fractal->createData($resource)->toArray();

        return response()->json($response, 200);
    }

    private function respondWithItem($item)
    {
        $resource = new Item($item, $this->transformer);
        $this->parseIncludes();
        $this->fractal->setSerializer(new ArraySerializer);
        $response = $this->fractal->createData($resource)->toArray();

        return response()->json($response, 200);
    }

    private function respondWithPaginator($paginator)
    {
        $data = $paginator->getCollection();
        $resource = new Collection($data, $this->transformer);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
        $this->parseIncludes();
        $this->fractal->setSerializer(new ArraySerializer);
        $response = $this->fractal->createData($resource)->toArray();

        return response()->json($response, 200);
    }

    private function parseIncludes()
    {
        if (request()->has('include')) {
            $this->fractal->parseIncludes(request()->get('include'));
        }
    }

    public function store(Request $request)
    {
         $item = $this->service->store($request->all());
        return $this->respondWithItem($item);
    }
}

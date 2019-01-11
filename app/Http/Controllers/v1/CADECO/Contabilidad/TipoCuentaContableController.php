<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 8/01/19
 * Time: 07:07 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contabilidad\TipoCuentaContableTransformer;
use App\Services\CADECO\Contabilidad\TipoCuentaContableService;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\ArraySerializer;

class TipoCuentaContableController extends Controller
{

    /**
     * @var TipoCuentaContableService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * TipoCuentaContableController constructor.
     * @param TipoCuentaContableService $service
     * @param Manager $fractal
     */
    public function __construct(TipoCuentaContableService $service, Manager $fractal)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
    }

    public function index(Request $request) {
        $data = $this->service->index();
        $resource = new Collection($data, new TipoCuentaContableTransformer);

        if ($request->has('include')) {
            $this->fractal->parseIncludes($request->get('include'));
        }

        $this->fractal->setSerializer(new ArraySerializer);
        $response = $this->fractal->createData($resource)->toArray();
        return response()->json($response, 200);
    }
}
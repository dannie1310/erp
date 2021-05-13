<?php


namespace App\Http\Controllers\v1\MODULOSSAO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\MODULOSSAO\ControlRemesas\RemesaFolioTransformer;
use App\Services\MODULOSSAO\RemesaFolioService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;
use Illuminate\Http\Request;

class RemesaFolioController extends Controller
{
    use ControllerTrait;

    /**
     * @var RemesaFolioTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var RemesaFolioService
     */
    protected $service;

    /**
     * RemesaFolioController constructor.
     * @param RemesaFolioTransformer $transformer
     * @param Manager $fractal
     * @param RemesaFolioService $service
     */
    public function __construct(RemesaFolioTransformer $transformer, Manager $fractal, RemesaFolioService $service)
    {
        $this->middleware('auth:api');
        $this->middleware('permisoGlobal:consultar_limite_remesa')->only(['show','paginate','index','find']);

        $this->transformer = $transformer;
        $this->fractal = $fractal;
        $this->service = $service;
    }

    public function show(Request $request)
    {
        $item = $this->service->show($request->all());
        return $this->respondWithItem($item);
    }

    public function update(Request $request)
    {
        $item = $this->service->update($request->all());
        return $this->respondWithItem($item);
    }
}

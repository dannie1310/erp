<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Fiscal\AutocorreccionTransformer;
use App\Services\SEGURIDAD_ERP\Fiscal\AutocorreccionService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class AutocorreccionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var AutocorreccionService
     */
    protected $service;

    /**
     * @var AutocorreccionTransformer
     */
    protected $transformer;

    /**
     * AutocorreccionController constructor.
     * @param Manager $fractal
     * @param AutocorreccionService $service
     * @param AutocorreccionTransformer $transformer
     */
    public function __construct(Manager $fractal, AutocorreccionService $service, AutocorreccionTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function aplicar(Request $request, $id)
    {
        return $this->respondWithItem($this->service->aplicar($id, $request->all()));
    }
}

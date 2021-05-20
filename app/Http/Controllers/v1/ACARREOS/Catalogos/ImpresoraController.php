<?php


namespace App\Http\Controllers\v1\ACARREOS\Catalogos;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\ACARREOS\Catalogos\ImpresoraService;
use App\Http\Transformers\ACARREOS\Catalogos\ImpresoraTransformer;

class ImpresoraController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ImpresoraService
     */
    protected $service;

    /**
     * @var ImpresoraTransformer
     */
    protected $transformer;

    /**
     * ImpresoraController constructor.
     * @param Manager $fractal
     * @param ImpresoraService $service
     * @param ImpresoraTransformer $transformer
     */
    public function __construct(Manager $fractal, ImpresoraService $service, ImpresoraTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_impresora')->only(['paginate','index']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

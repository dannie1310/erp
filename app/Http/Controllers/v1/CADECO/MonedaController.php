<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/03/19
 * Time: 05:33 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Services\CADECO\MonedaService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class MonedaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var MonedaService
     */
    protected $service;

    /**
     * @var MonedaTransformer
     */
    protected $transformer;

    /**
     * MonedaController constructor.
     * @param Manager $fractal
     * @param MonedaService $service
     * @param MonedaTransformer $transformer
     */
    public function __construct(Manager $fractal, MonedaService $service, MonedaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context')->except(['monedasGlobales']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function monedasGlobales(Request $request)
    {
        return $this->respondWithCollection($this->service->index($request->all()));
    }
}

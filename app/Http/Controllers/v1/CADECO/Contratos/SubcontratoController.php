<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 25/02/2019
 * Time: 07:05 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contratos;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Subcontratos\SubcontratoTransformer;
use App\Services\CADECO\Contratos\SubcontratoService;
use App\Traits\ControllerTrait;

class SubcontratoController extends Controller
{
    use ControllerTrait;

    /**
     * @var SubcontratoController
     */
    private $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SubcontratoTransformer
     */
    protected $transformer;

    /**
     * SubcontratoController constructor.
     * @param SubcontratoService $service
     * @param Manager $fractal
     * @param SubcontratoTransformer $transformer
     */
    public function __construct(SubcontratoService $service, Manager $fractal, SubcontratoTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function allSubcontratosSinFondo(Request $request)
    {
        $collection = $this->service->allSubcontratosSinFondo($request->all());
        return $this->respondWithCollection($collection);
    }
}
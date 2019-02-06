<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 05:40 PM
 */

namespace App\Http\Controllers\v1\CADECO\Tesoreria;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMovimientoBancarioRequest;
use App\Http\Transformers\CADECO\Tesoreria\MovimientoBancarioTransformer;
use App\Services\CADECO\Tesoreria\MovimientoBancarioService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class MovimientoBancarioController extends Controller
{
    use ControllerTrait {
        store as protected traitStore;
    }

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var MovimientoBancarioService
     */
    protected $service;

    /**
     * @var MovimientoBancarioTransformer
     */
    protected $transformer;

    /**
     * MovimientoBancarioController constructor.
     * @param Manager $fractal
     * @param MovimientoBancarioService $service
     * @param MovimientoBancarioTransformer $transformer
     */
    public function __construct(Manager $fractal, MovimientoBancarioService $service, MovimientoBancarioTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function store(StoreMovimientoBancarioRequest $request)
    {
        return $this->traitStore($request);
    }
}
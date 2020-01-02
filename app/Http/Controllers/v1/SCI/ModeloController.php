<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 28/10/2019
 * Time: 08:20 p. m.
 */


namespace App\Http\Controllers\v1\SCI;

use App\Http\Controllers\Controller;
use App\Http\Transformers\SCI\ModeloTransformer;
use App\Services\SCI\ModeloService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ModeloController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ModeloService
     */
    protected $service;

    /**
     * @var ModeloTransformer
     */
    protected $transformer;


    /**
     * ModeloController constructor
     * @param Manager $fractal
     * @param ModeloService $service
     * @param ModeloTransformer $transformer
     */

    public function __construct(Manager $fractal, ModeloService $service, ModeloTransformer $transformer)
    {

        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }


}

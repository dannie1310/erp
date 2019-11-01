<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 28/10/2019
 * Time: 08:20 p. m.
 */


namespace App\Http\Controllers\v1\SCI;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SCI\MarcaTransformer;
use App\Services\SCI\MarcaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class MarcaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var MarcaService
     */
    protected $service;

    /**
     * @var MarcaTransformer
     */

    protected $transformer;


    /**
     *MarcaController constructor
     * @param Manager $fractal
     * @param MarcaService $service
     * @param MarcaTransformer $transformer
     *
     */


    public function __construct(Manager $fractal, MarcaService $service, MarcaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

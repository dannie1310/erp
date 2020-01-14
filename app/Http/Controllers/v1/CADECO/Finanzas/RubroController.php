<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/01/2020
 * Time: 07:54 PM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Transformers\CADECO\Finanzas\RubroTransformer;
use App\Http\Controllers\Controller;
use App\Services\CADECO\Finanzas\RubroService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class RubroController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var RubroService
     */
    protected $service;


    /**
     * @var RubroTransformer
     */
    protected $transformer;


    /**
     * RubroController constructor.
     * @param Manager $fractal
     * @param RubroService $service
     * @param RubroTransformer $transformer
     */
    public function __construct(Manager $fractal, RubroService $service, RubroTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}
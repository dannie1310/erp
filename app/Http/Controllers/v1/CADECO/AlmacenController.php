<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 07/02/19
 * Time: 04:51 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\AlmacenTransformer;
use App\Services\CADECO\AlmacenService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class AlmacenController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var AlmacenService
     */
    protected $service;

    /**
     * @var AlmacenTransformer
     */
    protected $transformer;

    /**
     * AlmacenController constructor.
     *
     * @param Manager $fractal
     * @param AlmacenService $service
     * @param AlmacenTransformer $transformer
     */
    public function __construct(Manager $fractal, AlmacenService $service, AlmacenTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
<?php

namespace App\Http\Controllers\v1\IGH;

use App\Http\Controllers\Controller;
use App\Http\Transformers\IGH\MenuTransformer;
use App\Services\IGH\MenuService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class MenuController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var MenuService
     */
    protected $service;

    /**
     * @var MenuTransformer
     */
    protected $transformer;

    public function __construct(Manager $fractal, MenuService $service, MenuTransformer $transformer)
    {
        $this->middleware('auth');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
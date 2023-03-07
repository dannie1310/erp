<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Fiscal;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\Fiscal\NotificacionREPTransformer;
use App\Services\SEGURIDAD_ERP\Fiscal\NotificacionREPService;
use League\Fractal\Manager;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;


class NotificacionREPController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var Service|NotificacionREPService
     */
    protected $service;

    /**
     * @var Transformer|NotificacionREPTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param NotificacionREPService $service
     * @param NotificacionREPTransformer $transformer
     */
    public function __construct(Manager $fractal, NotificacionREPService $service, NotificacionREPTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

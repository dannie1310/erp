<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Documentacion;

use App\Http\Controllers\Controller;
use App\Traits\ControllerTrait;
use App\Http\Transformers\SEGURIDAD_ERP\Documentacion\CtgTipoTransaccionTransformer as Transformer;
use App\Services\SEGURIDAD_ERP\Documentacion\CtgTipoTransaccionService as Service;
use League\Fractal\Manager;

class CtgTipoTransaccionController extends Controller
{
    use ControllerTrait;

    public function __construct(Service $service, Manager $fractal, Transformer $transformer)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }





}

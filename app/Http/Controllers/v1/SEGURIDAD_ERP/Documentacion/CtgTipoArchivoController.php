<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Documentacion;

use App\Http\Controllers\Controller;
use App\Traits\ControllerTrait;
use App\Http\Transformers\SEGURIDAD_ERP\Documentacion\CtgTipoArchivoTransformer as Transformer;

use League\Fractal\Manager;
use App\Services\SEGURIDAD_ERP\Documentacion\CtgTipoArchivoService as Service;

class CtgTipoArchivoController extends Controller
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

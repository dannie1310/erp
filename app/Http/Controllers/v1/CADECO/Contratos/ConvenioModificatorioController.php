<?php


namespace App\Http\Controllers\v1\CADECO\Contratos;

use App\Http\Controllers\Controller;
use App\Traits\ControllerTrait;
use App\Services\CADECO\Contratos\ConvenioModificatorioService as Service;
use League\Fractal\Manager;
use App\Http\Transformers\CADECO\SubcontratosCM\TransaccionTransformer as Transformer;

class ConvenioModificatorioController extends Controller
{
    use ControllerTrait;

    public function __construct(Service $service, Manager $fractal, Transformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        /*$this->middleware('permiso:consultar_convenio_modificatorio')->only(['index','paginate','find','show', 'pdf']);
        $this->middleware('permiso:registrar_convenio_modificatorio')->only(['store']);
        $this->middleware('permiso:eliminar_convenio_modificatorio')->only('destroy');*/

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

}

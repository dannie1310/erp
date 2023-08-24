<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\DocumentoTransformer;
use App\Services\CONTROLRECURSOS\DocumentoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class DocumentoController extends Controller
{
    use ControllerTrait;

    /**
     * @var DocumentoService
     */
    protected $service;

    /**
     * @var DocumentoTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param DocumentoService $service
     * @param DocumentoTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(DocumentoService $service, DocumentoTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');
        $this->middleware('permisoGlobal:consultar_factura_recursos')->only(['show','paginate','index']);
        $this->middleware('permisoGlobal:registrar_factura_recursos')->only(['store']);

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

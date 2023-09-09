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

        $this->middleware('permisoGlobal:consultar_documento_recursos')->only(['show','paginate','index']);
        $this->middleware('permisoGlobal:registrar_documento_recursos')->only(['store']);
        $this->middleware('permisoGlobal:editar_documento_recursos')->only(['update']);
        $this->middleware('permisoGlobal:eliminar_documento_recursos')->only(['destroy']);

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}

<?php

namespace App\Http\Controllers\v1\ACTIVO_FIJO;

use App\Http\Controllers\Controller;
use App\Http\Transformers\ACTIVO_FIJO\PartidaRegistradaTransformer;
use App\Services\ACTIVO_FIJO\PartidaRegistradaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class PartidaRegistradaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var PartidaRegistradaService
     */
    protected $service;

    /**
     * @var PartidaRegistradaTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param PartidaRegistradaService $service
     * @param PartidaRegistradaTransformer $transformer
     */
    public function __construct(Manager $fractal, PartidaRegistradaService $service, PartidaRegistradaTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function pdfEtiqueta($id)
    {
        return $this->service->pdfEtiquetas($id);
    }
}

<?php

namespace App\Http\Controllers\v1\CONTROLRECURSOS;

use App\Http\Controllers\Controller;
use App\Http\Transformers\CONTROLRECURSOS\ReembolsoGastoSolTransformer;
use App\Services\CONTROLRECURSOS\ReembolsoGastoSolService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class ReembolsoGastoSolController extends Controller
{
    use ControllerTrait;

    /**
     * @var ReembolsoGastoSolService
     */
    protected $service;

    /**
     * @var ReembolsoGastoSolTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @param ReembolsoGastoSolService $service
     * @param ReembolsoGastoSolTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(ReembolsoGastoSolService $service, ReembolsoGastoSolTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->middleware('permisoGlobal:consultar_documento_recursos')->only(['show','paginate','index']);

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }

    public function validarDocumentos(Request $request, $id)
    {
        return $this->respondWithItem($this->service->validarDocumentos($id));
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: JLopeza
 * Date: 15/07/2020
 * Time: 02:15 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contratos;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\CADECO\Contratos\AsignacionContratistaService;
use App\Http\Transformers\CADECO\Contrato\AsignacionContratistaTransformer;

class AsignacionContratistaController extends Controller
{
    use ControllerTrait;

    /**
     * @var AsignacionContratistaService
     */
    private $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var AsignacionContratistaTransformer
     */
    protected $transformer;

    /**
     * SubcontratoController constructor.
     * @param AsignacionContratistaService $service
     * @param Manager $fractal
     * @param AsignacionContratistaTransformer $transformer
     */
    public function __construct(AsignacionContratistaService $service, Manager $fractal, AsignacionContratistaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        // $this->middleware('permiso:consultar_subcontrato')->only(['show', 'paginate']);

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 25/02/2019
 * Time: 07:05 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contratos;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\CADECO\Contratos\TipoContratoService;
use App\Http\Transformers\CADECO\Subcontrato\TipoContratoTransformer;

class TipoContratoController extends Controller
{
    use ControllerTrait;

    /**
     * @var TipoContratoService
     */
    private $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TipoContratoTransformer
     */
    protected $transformer;

    /**
     * SubcontratoController constructor.
     * @param TipoContratoService $service
     * @param Manager $fractal
     * @param TipoContratoTransformer $transformer
     */
    public function __construct(TipoContratoService $service, Manager $fractal, TipoContratoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

}

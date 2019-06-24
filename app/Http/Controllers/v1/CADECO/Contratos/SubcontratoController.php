<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 25/02/2019
 * Time: 07:05 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contratos;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contrato\SubcontratoTransformer;
use App\Services\CADECO\Contratos\SubcontratoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;

class SubcontratoController extends Controller
{
    use ControllerTrait;

    /**
     * @var SubcontratoService
     */
    private $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SubcontratoTransformer
     */
    protected $transformer;

    /**
     * SubcontratoController constructor.
     * @param SubcontratoService $service
     * @param Manager $fractal
     * @param SubcontratoTransformer $transformer
     */
    public function __construct(SubcontratoService $service, Manager $fractal, SubcontratoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function getConceptosNuevaEstimacion(Request $request, $id_subcontrato)
    {
        $conceptos = DB::connection('cadeco')
            ->select(DB::raw("EXEC [SubcontratosEstimaciones].[uspConceptosEstimacion] {$id_subcontrato}, null, 0, 0"));
        return response()->json($conceptos);
    }
}
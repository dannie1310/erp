<?php


namespace App\Http\Controllers\v1\MODULOSSAO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\MODULOSSAO\ControlRemesas\DocumentoDeNoLocalizadoTransformer;
use App\Services\MODULOSSAO\DocumentoDeNoLocalizadoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class DocumentoDeNoLocalizadoController extends Controller
{
    use ControllerTrait;

    /**
     * @var DocumentoDeNoLocalizadoService
     */
    protected $service;

    /**
     * @var DocumentoDeNoLocalizadoTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * DocumentoDeNoLocalizadoController constructor.
     * @param DocumentoDeNoLocalizadoService $service
     * @param DocumentoDeNoLocalizadoTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(DocumentoDeNoLocalizadoService $service, DocumentoDeNoLocalizadoTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }

    public function autorizar(Request $request, $id)
    {
        return $this->respondWithItem($this->service->autorizar($id));
    }

    public function rechazar(Request $request, $id)
    {
        return $this->respondWithItem($this->service->rechazar($request->all(),$id));
    }
}

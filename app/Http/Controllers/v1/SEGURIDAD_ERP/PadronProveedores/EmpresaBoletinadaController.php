<?php

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores;

use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaTransformer;
use App\Services\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaService;
use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;

class EmpresaBoletinadaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var EmpresaBoletinadaService
     */
    protected $service;

    /**
     * @var EmpresaBoletinadaTransformer
     */
    protected $transformer;

    /**
     * @param Manager $fractal
     * @param EmpresaBoletinadaService $service
     * @param EmpresaBoletinadaTransformer $transformer
     */
    public function __construct(Manager $fractal, EmpresaBoletinadaService $service, EmpresaBoletinadaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function revisarRFC(Request $request, $id)
    {
        return $this->service->revisarRFC($request->all()['rfc'], $id);
    }

    public function showRFC(Request $request, $rfc)
    {
        if(auth()->user()->usuario == $rfc){
            $empresa =  $this->service->buscaPorRFC($rfc);
            if($empresa){
                $item = $this->service->show($empresa->id);
                return $this->respondWithItem($item);
            }
            else{
                $item = $this->service->generaExpediente($rfc);
                return $this->respondWithItem($item);
            }

        } else {
            return response()->json("No esta autorizado a consultar esta información", 200);
        }
    }
}

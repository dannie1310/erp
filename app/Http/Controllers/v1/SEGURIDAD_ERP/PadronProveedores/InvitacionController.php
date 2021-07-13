<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP\PadronProveedores;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;

use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\InvitacionTransformer;
use App\Services\SEGURIDAD_ERP\PadronProveedores\InvitacionService;

class InvitacionController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var EmpresaService
     */
    protected $service;

    /**
     * @var EmpresaTransformer
     */
    protected $transformer;

    /**
     * EmpresaController constructor.
     * @param Manager $fractal
     * @param EmpresaService $service
     * @param EmpresaTransformer $transformer
     */
    public function __construct(Manager $fractal, EmpresaService $service, EmpresaTransformer $transformer)
    {
        $this->middleware('auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function registrarPrestadora(Request $request){
        return $this->service->registrarPrestadora($request->all());
    }

    public function revisarRFC(Request $request, $id)
    {
        return $this->service->revisarRFC($request->all()['rfc'], $id);
    }

    public function revisarRfcPrestadora(Request $request)
    {
        return $this->service->revisarRfcPrestadora($request->all());
    }

    public function revisarRFCPreexistente(Request $request)
    {
        return $this->service->revisarRFCPreexistente($request->all()['rfc']);
    }

    public function descargaExpediente(Request $request, $id)
    {
        return $this->service->descargaExpediente($id);
    }
}

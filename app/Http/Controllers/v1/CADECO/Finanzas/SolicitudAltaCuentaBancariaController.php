<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 07/08/2019
 * Time: 05:09 PM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Requests\Finanzas\StoreSolicitudAltaCuentaBancariaRequest;
use App\Http\Transformers\CADECO\Finanzas\SolicitudAltaCuentaBancariaTransformer;
use App\Services\CADECO\Finanzas\SolicitudAltaCuentaBancariaService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class SolicitudAltaCuentaBancariaController extends Controller
{
    use ControllerTrait{
        store as protected traitStore;
    }

    /**
     * @var SolicitudAltaCuentaBancariaService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var SolicitudAltaCuentaBancariaTransformer
     */
    private $transformer;

    /**
     * SolicitudAltaCuentaBancariaController constructor.
     * @param SolicitudAltaCuentaBancariaService $service
     * @param Manager $fractal
     * @param SolicitudAltaCuentaBancariaTransformer $transformer
     */
    public function __construct(SolicitudAltaCuentaBancariaService $service, Manager $fractal, SolicitudAltaCuentaBancariaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_solicitud_alta_cuenta_bancaria_empresa')->only(['show','paginate','index','find','pdf']);
        $this->middleware('permiso:solicitar_alta_cuenta_bancaria_empresa')->only('store');
//        $this->middleware('permiso:rechazar_solicitud_alta_cuenta_bancaria_empresa')->only('');
//        $this->middleware('permiso:cancelar_solicitud_alta_cuenta_bancaria_empresa')->only('cancelar');
//        $this->middleware('permiso:autorizar_solicitud_alta_cuenta_bancaria_empresa')->only('autorizar');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function store(StoreSolicitudAltaCuentaBancariaRequest $request)
    {
        return $this->traitStore($request);
    }

    public function pdf($id){
        return $this->service->pdf($id);
    }

    public function autorizar ($id){
        return $this->service->autorizar($id);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 26/02/2020
 * Time: 03:45 PM
 */

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad;


use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\EmpresaSATTransformer;
use App\Services\SEGURIDAD_ERP\Contabilidad\EmpresaSATService;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;
use Illuminate\Http\Request;

class EmpresaSATController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var EmpresaSATService
     */
    protected $service;

    /**
     * @var EmpresaSATTransformer
     */
    protected $transformer;

    /**
     * EmpresaSATController constructor.
     * @param Manager $fractal
     * @param EmpresaSATService $service
     * @param EmpresaSATTransformer $transformer
     */
    public function __construct(Manager $fractal, EmpresaSATService $service, EmpresaSATTransformer $transformer)
    {
        $this->middleware( 'auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function cargaZIP(Request $request)
    {
        /*dd($request->nombre_archivo,
            $request->id_empresa,
            $request->archivo_zip,
            $request->file("archivo_zip"));*/
        $file = $request->file("archivo_zip");
       // dd($file["name"]);
        $this->service->procesaZIPCFD($request->archivo_zip);
    }
}
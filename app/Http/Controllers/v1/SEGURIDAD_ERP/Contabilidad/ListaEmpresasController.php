<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 20/02/2020
 * Time: 6:46 PM
 */

namespace App\Http\Controllers\v1\SEGURIDAD_ERP\Contabilidad;


use League\Fractal\Manager;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Services\SEGURIDAD_ERP\Contabilidad\ListaEmpresasService;
use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\ListaEmpresasTransformer;

class ListaEmpresasController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ListaEmpresasService
     */
    protected $service;

    /**
     * @var ListaEmpresasTransformer
     */
    protected $transformer;

    /**
     * IncidenciaController constructor.
     * @param Manager $fractal
     * @param ListaEmpresasService $service
     * @param ListaEmpresasTransformer $transformer
     */
    public function __construct(Manager $fractal, ListaEmpresasService $service, ListaEmpresasTransformer $transformer)
    {
        $this->middleware( 'auth:api');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}
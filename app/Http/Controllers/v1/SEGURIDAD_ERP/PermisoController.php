<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/25/19
 * Time: 6:27 PM
 */

namespace App\Http\Controllers\v1\SEGURIDAD_ERP;


use App\Http\Controllers\Controller;
use App\Http\Transformers\SEGURIDAD_ERP\PermisoTransformer;
use App\Services\SEGURIDAD_ERP\PermisoService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class PermisoController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var PermisoService
     */
    protected $service;

    /**
     * @var PermisoTransformer
     */
    protected $transformer;

    /**
     * PermisoController constructor.
     *
     * @param Manager $fractal
     * @param PermisoService $service
     * @param PermisoTransformer $transformer
     */
    public function __construct(Manager $fractal, PermisoService $service, PermisoTransformer $transformer)
    {
        $this->middleware( 'auth:api');
        $this->middleware( 'context' )->except(['paginate','porCantidad','porObra']);

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function porUsuario(Request $request, $id)
    {
        return $this->service->porUsuario($id);
    }

    public function porObra(Request $request, $id)
    {
        return $this->service->porObra($id);
    }
    public function porCantidad(Request $request)
    {
        return $this->service->porCantidad();
    }
}
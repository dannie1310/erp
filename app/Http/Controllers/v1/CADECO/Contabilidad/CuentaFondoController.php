<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 11/01/19
 * Time: 05:03 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contabilidad\CuentaFondoTransformer;
use App\Services\CADECO\Contabilidad\CuentaFondoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;
use Illuminate\Http\Request;

class CuentaFondoController extends Controller
{
    use ControllerTrait {
        update as protected traitUpdate;
    }

    /**
     * @var CuentaFondoService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CuentaFondoTransformer
     */
    private $transformer;

    /**
     * CuentaFondoController constructor.
     * @param CuentaFondoService $service
     * @param Manager $fractal
     * @param CuentaFondoTransformer $transformer
     */
    public function __construct(CuentaFondoService $service, Manager $fractal, CuentaFondoTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function update(Request $request, $id)
    {
        return $this->traitUpdate($request, $id);
    }
}
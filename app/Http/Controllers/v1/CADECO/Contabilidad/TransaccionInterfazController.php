<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 01/04/19
 * Time: 02:07 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Contabilidad\TransaccionInterfazTransformer;
use App\Services\CADECO\Contabilidad\TransaccionInterfazService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TransaccionInterfazController extends Controller
{
    use ControllerTrait;

    /**
     * @var TransaccionInterfazService
     */
    private $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var TransaccionInterfazTransformer
     */
    protected $transformer;

    /**
     * TransaccionInterfazController constructor.
     * @param TransaccionInterfazService $service
     * @param Manager $fractal
     * @param TransaccionInterfazTransformer $transformer
     */
    public function __construct(TransaccionInterfazService $service, Manager $fractal, TransaccionInterfazTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
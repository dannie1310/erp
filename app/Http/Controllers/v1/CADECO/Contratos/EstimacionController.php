<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 02:15 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contratos;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\EstimacionTransformer;
use App\Services\CADECO\EstimacionService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class EstimacionController extends Controller
{
    use ControllerTrait;

    /**
     * @var EstimacionService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var EstimacionTransformer
     */
    protected $transformer;

    /**
     * EstimacionController constructor.
     * @param EstimacionService $service
     * @param Manager $fractal
     * @param EstimacionTransformer $transformer
     */
    public function __construct(EstimacionService $service, Manager $fractal, EstimacionTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function pdfOrdenPago($id)
    {
        return $this->service->pdfOrdenPago($id)->create();
    }

}
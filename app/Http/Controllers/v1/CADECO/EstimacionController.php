<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 02:15 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\EstimacionTransformer;
use App\PDF\Formato\OrdenPagoEstimacion;
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

    public function pdf($id)
    {
        $estimacion = $this->service->find($id);
        dd("AQUI", $estimacion);
        $pdf = new OrdenPagoEstimacion($estimacion);
        $pdf->create();

    }
}
<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 17/09/2019
 * Time: 06:18 PM
 */

namespace App\Http\Controllers\v1\CADECO\Almacenes;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Almacenes\AjusteTransformer;
use App\Services\CADECO\Almacenes\AjusteService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class AjusteController extends Controller
{
    use ControllerTrait;

    /**
     * @var AjusteService
     */
    protected $service;

    /**
     * @var AjusteTransformer
     */
    protected $transformer;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * AjusteController constructor.
     * @param AjusteService $service
     * @param AjusteTransformer $transformer
     * @param Manager $fractal
     */
    public function __construct(AjusteService $service, AjusteTransformer $transformer, Manager $fractal)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->transformer = $transformer;
        $this->fractal = $fractal;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 08:41 PM
 */

namespace App\Http\Controllers\v1\CADECO\Almacenes;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Almacenes\AjustePositivoTransformer;
use App\Services\CADECO\Almacenes\AjustePositivoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class AjustePositivoController extends Controller
{
    use ControllerTrait;

    /**
     * @var AjustePositivoService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var AjustePositivoTransformer
     */
    protected $transformer;

    /**
     * AjustePositivoController constructor.
     * @param AjustePositivoService $service
     * @param Manager $fractal
     * @param AjustePositivoTransformer $transformer
     */
    public function __construct(AjustePositivoService $service, Manager $fractal, AjustePositivoTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
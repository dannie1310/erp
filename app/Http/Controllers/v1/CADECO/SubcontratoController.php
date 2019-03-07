<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 07/03/2019
 * Time: 10:05 AM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\SubcontratoTransformer;
use App\Services\CADECO\SubcontratoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SubcontratoController extends Controller
{
    use ControllerTrait;

    /**
     * @var SubcontratoService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var SubcontratoTransformer
     */
    protected $transformer;

    /**
     * SubcontratoController constructor.
     * @param SubcontratoService $service
     * @param Manager $fractal
     * @param SubcontratoTransformer $transformer
     */
    public function __construct(SubcontratoService $service, Manager $fractal, SubcontratoTransformer $transformer)
    {
        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}
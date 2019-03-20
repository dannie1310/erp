<?php
/**
 * Created by PhpStorm.
 * User: Luis Valencia
 * Date: 13/03/2019
 * Time: 11:46 AM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\ObraTransformer;
use App\Services\CADECO\ObraService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class ObraController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ObraService
     */
    protected $service;

    /**
     * @var ObraTransformer
     */
    protected $transformer;

    /**
     * ObraController constructor.
     * @param Manager $fractal
     * @param ObraService $service
     * @param ObraTransformer $transformer
     */
    public function __construct(Manager $fractal, ObraService $service, ObraTransformer $transformer)
    {
        $this->middleware('auth');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function authPaginate(Request $request)
    {
        $paginator = $this->service->authPaginate();
        return $this->respondWithPaginator($paginator);
    }
}
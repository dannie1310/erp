<?php


namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\DestajistaTransformer;
use App\Services\CADECO\DestajistaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class DestajistaController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var DestajistaService
     */
    protected $service;

    /**
     * @var DestajistaTransformer
     */
    protected $transformer;

    /**
     * DestajistaController constructor.
     * @param Manager $fractal
     * @param DestajistaService $service
     * @param DestajistaTransformer $transformer
     */
    public function __construct(Manager $fractal, DestajistaService $service, DestajistaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->middleware('permiso:consultar_destajista')->only(['show','paginate','index','find']);
        $this->middleware('permiso:registrar_destajista')->only('store');
        $this->middleware('permiso:editar_destajista')->only('update');
        $this->middleware('permiso:eliminar_destajista')->only('destroy');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }
}

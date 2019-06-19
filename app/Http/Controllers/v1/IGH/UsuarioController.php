<?php

namespace App\Http\Controllers\v1\IGH;

use App\Http\Controllers\Controller;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\IGH\Usuario;
use App\Services\IGH\UsuarioService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class UsuarioController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var UsuarioService
     */
    protected $service;

    /**
     * @var UsuarioTransformer
     */
    protected $transformer;

    public function __construct(Manager $fractal, UsuarioService $service, UsuarioTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context')->except('currentUser');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function currentUser(Request $request ) {
        return response()->json(['user' => Usuario::query()->find(auth()->id())]);
    }
}
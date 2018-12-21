<?php

namespace App\Http\Controllers\v1;

use App\Facades\Context;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SetContextRequest;
use App\Models\CADECO\Obra;
use App\Services\AuthService;
use App\Traits\AuthenticatesIghUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use AuthenticatesIghUsers;

    /**
     * @var AuthService
     */
    private $auth;

    /**
     * AuthController constructor.
     * @param AuthService $auth
     */
    public function __construct(AuthService $auth)
    {
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->middleware('context', ['except' => ['login', 'setContext', 'refresh', 'obras']]);

        $this->auth = $auth;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['usuario', 'clave']);

        $token = $this->auth->login($credentials);

        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    /**
     * @param SetContextRequest $request
     * @return JsonResponse
     */
    public function setContext(SetContextRequest $request)
    {
        $new_token = $this->auth->setContext($request->only(['database', 'id_obra']));
        return response()->json([
            'access_token' => $new_token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'obra' => Obra::with('datosContables')->find($request->id_obra)
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getContext()
    {
        return response()->json([
            'message' => 'context is established',
            'obra' => Obra::find(Context::getIdObra()),
            'permisos' => auth()->user()->permisos()

        ], 200);
    }

        /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        return response()->json([
            'access_token' => auth()->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function obras() {
        $obras = $this->auth->getObras();

        return response()->json($obras, 200);
        }
}
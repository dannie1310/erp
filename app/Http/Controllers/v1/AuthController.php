<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SetContextRequest;
use App\Services\AuthService;
use App\Traits\AuthenticatesIghUsers;
use Illuminate\Http\JsonResponse;

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
        $this->middleware('context', ['except' => ['login', 'context']]);
        $this->middleware('refresh', ['only' => 'me']);

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
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * @param SetContextRequest $request
     * @return JsonResponse
     */
    public function context(SetContextRequest $request)
    {
        $new_token = $this->auth->setContext($request->only(['database', 'id_obra']));
        return $this->respondWithToken($new_token);
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function me() {
        return auth()->user();
    }
}
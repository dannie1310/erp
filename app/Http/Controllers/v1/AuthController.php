<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SetContextRequest;
use App\Services\ContextService;
use App\Traits\AuthenticatesIghUsers;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use AuthenticatesIghUsers;

    /**
     * @var ContextService
     */
    private $context;

    /**
     * AuthController constructor.
     * @param ContextService $context
     */
    public function __construct(ContextService $context)
    {
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->middleware('context', ['except' => ['login', 'context']]);

        $this->context = $context;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['usuario', 'clave']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

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
        $new_token = $this->context->setContext($request->only(['database', 'id_obra']));
        return $this->respondWithToken($new_token);
    }
}
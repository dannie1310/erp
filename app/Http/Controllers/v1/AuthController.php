<?php

namespace App\Http\Controllers\v1;

use App\Facades\Context;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SetContextRequest;
use App\Models\CADECO\Obra;
use App\Services\AuthService;
use App\Traits\AuthenticatesIghUsers;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $this->middleware('auth:api', ['except' => ['login', 'setContext', 'movil']]);
        $this->middleware('context', ['except' => ['login', 'logout', 'setContext', 'refresh', 'obras', 'movil']]);

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
        $credentials = request(['usuario', 'clave']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'], 401);
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        return $this->respondWithToken($tokenResult);
    }

    public function movil(Request $request){
        dd(1);
        $credentials = request(['usuario', 'clave']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'], 401);
        }

        $user = $request->user();
        dd($user);
        $tokenResult = $user->createToken('Personal Access Tokens');
        $token = $tokenResult->token;
        $token->save();

        return $this->respondWithToken($tokenResult);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($tokenResult)
    {
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_in'   => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'user'         => request()->user()
        ]);
    }

    /**
     * @param SetContextRequest $request
     * @return JsonResponse
     */
    public function setContext(SetContextRequest $request)
    {
        $this->auth->setContext(["db"=>$request->db
            , "id_obra"=>$request->id_obra
            , "db_cntpq"=>""]);

        $obra = Obra::query()->find($request->id_obra);

        if (! $obra->datosContables) {
            $obra->datosContables()->create();
        }else{
            $this->auth->setContext(["db"=>$request->db
                , "id_obra"=>$request->id_obra
                , "db_cntpq"=>($obra->datosContables->BDContPaq) ? $obra->datosContables->BDContPaq : '']
            );
        }

        /*
         * return response()->json([
            'obra' => Obra::with(['datosContables', 'configuracion'])->find($request->id_obra),
        * 'permisos' => auth()->user()->permisos()
        ]);
         * */

        $obra2 = Obra::with(['datosContables.empresa', 'configuracion'])
            ->where("id_obra","=",$request->id_obra)
            ->select('nombre',
                'constructora',
                'cliente',
                'facturar',
                'responsable',
                'rfc',
                'id_moneda',
                'iva',
                'fecha_inicial',
                'fecha_final',
                'tipo_obra',
                'descripcion',
                'id_obra'

                )->first();
        return response()->json([
            'obra' => $obra2,
            'permisos' => auth()->user()->permisos()
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getContext()
    {
        return response()->json([
            'message' => 'Obra establecida correctamente',
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

<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

class Google2faController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function qr()
    {
        return redirect(GoogleQrUrl::generate(auth()->user()->usuario, auth()->user()->google2faSecret->secret, 'SAO-ERP'));
    }

    public function check(Request $request)
    {
        $g = new GoogleAuthenticator();
        if ($g->checkCode(auth()->user()->google2faSecret->secret, $request->code)) {
            auth()->user()->google2faSecret->verified = true;
            auth()->user()->google2faSecret->save();
            return response()->json([
                'message' => 'Código Válido',
                'valid' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'Código Inválido',
                'valid' => false
            ], 200);
        }
    }
    
    public function isVerified()
    {
        if (auth()->user()->google2faSecret) {
            if (auth()->user()->google2faSecret->verified)
                return response()->json([
                    'message' => 'Verified',
                    'verified' => true,
                    'status_code' => 200,
                ]);
            else {
                return response()->json([
                    'message' => 'Not verified',
                    'verified' => false,
                    'status_code' => 200,
                ]);
            }
        }

        return response()->json([
            'message' => 'Not verified',
            'verified' => true,
            'status_code' => 200,
        ]);
    }

    public function code(Request $request)
    {
        $g = new GoogleAuthenticator();
        return response()->json(['code' => $g->getCode(auth()->user()->google2faSecret->secret)]);
    }
}
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
        $this->middleware('addAccessToken')->only('qr');
        $this->middleware('auth:api');
    }

    public function qr()
    {
        return redirect(GoogleQrUrl::generate(auth()->user()->usuario, auth()->user()->google2faSecret->secret, 'SAO-ERP'));
        $image = file_get_contents($url);
        return response()->json(['qr' => base64_encode($image)]);
    }

    public function check(Request $request)
    {
        $g = new GoogleAuthenticator();
        if ($g->checkCode(auth()->user()->google2faSecret->secret, $request->code)) {
            return response()->json(['message' => 'success'], 200);
        } else {
            return response()->json(['message' => 'Code Invalid'], 400);
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
                    'verified' => true,
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
}
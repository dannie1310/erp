<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP;


use App\Facades\Context;
use App\Http\Controllers\Controller;
use App\Models\CADECO\Obra;
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
        /*$image = file_get_contents($url);
        return response()->json(['qr' => base64_encode($image)]);*/
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
}
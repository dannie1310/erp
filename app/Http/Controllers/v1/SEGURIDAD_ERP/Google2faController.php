<?php


namespace App\Http\Controllers\v1\SEGURIDAD_ERP;


use App\Facades\Context;
use App\Http\Controllers\Controller;
use App\Models\CADECO\Obra;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

class Google2faController extends Controller
{
    public function __construct()
    {
        $this->middleware('context');
    }

    public function generateQR()
    {
        $obra = Obra::query()->find(Context::getIdObra());
        return redirect(GoogleQrUrl::generate(auth()->user()->usuario, auth()->user()->google2faSecret->secret, 'SAO-ERP (' . $obra->nombre . ')'));
    }
}
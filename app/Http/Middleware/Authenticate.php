<?php

namespace App\Http\Middleware;

use App\Models\IGH\Usuario;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        $uri = $request->path();
        $credentials = $request->all();
        if(array_key_exists('usuario', $credentials) && array_key_exists('clave', $credentials)){
            $this->validaPasswordGenerico($uri, $credentials);
        }
        return view('auth.cambio_contrasena_temporal');
        dd('stop');
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    private function validaPasswordGenerico($URI, $credenciales){
        $usuario = Usuario::where('usuario', '=', $credenciales['usuario'])->first();
        // dd($usuario->idgenero == 1);
        if($usuario->idgenero == 1){
            // return route('temporal');
            return view('auth.cambio_contrasena_temporal');
        }
        dd(123, $credenciales['usuario'], $usuario);
        
    }
}
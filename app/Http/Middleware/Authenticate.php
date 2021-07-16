<?php

namespace App\Http\Middleware;

use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\Session;
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
        if(array_key_exists('usuario', $credentials)){
            Session::put('path', $uri);
            Session::put('credenciales', $credentials);
        }
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

}
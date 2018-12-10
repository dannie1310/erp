<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['middleware' => 'api', 'prefix' => 'auth'], function ($api) {
        $api->post('login', 'App\Http\Controllers\v1\AuthController@login');
        $api->post('logout', 'App\Http\Controllers\v1\AuthController@logout');
        $api->post('context', 'App\Http\Controllers\v1\AuthController@context');
        $api->post('refresh', 'App\Http\Controllers\v1\AuthController@refresh');
    });
});